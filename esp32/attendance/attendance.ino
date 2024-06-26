// Attendance

// WiFi config
// const char* ssid = "TP-Link_95A7";
// const char* password = "98568758";
// const char* ssid = "TP-Link_12B6";
// const char* password = "66303336";
// const char* ssid = "JAM";
// const char* password = "C3dricJ0yce";
// const char* ssid = "home broadband";
// const char* password = "Jacob1234***";
// const char *ssid = "TP-Link_5118";
// const char *password = "93206389";
const char *ssid = "PLDTHOMEFIBRd1572b";
const char *password = "123abc123";

// Server config
const String serverIP = "192.168.71.152";
const String phpScript = "2FA-Attendance-System/services/record_attendance.php";
const String destinationUrl = "http://" + serverIP + "/" + phpScript;

// Device config
const String room = "312";
const String title = "RM" + room + " Attendance";

// WiFi libraries
#include <WiFi.h>
#include <HTTPClient.h>

// SPI and MFRC522 libraries
#include <SPI.h>
#include <MFRC522.h>

// Wire for I2C and LCD libraries
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

// JSON library
#include <ArduinoJson.h>

// LCD config
#define LCD_ADDRESS 0x27
#define LCD_COLUMNS 16
#define LCD_ROWS 2
LiquidCrystal_I2C lcd(LCD_ADDRESS, LCD_COLUMNS, LCD_ROWS);

// RC522 config
#define SS_PIN 5
#define RST_PIN 21
MFRC522 mfrc522(SS_PIN, RST_PIN);
#define ON_Board_LED 2

// Buzzer config
#define buzzer 16

// Variables
int readsuccess;
byte readcard[4];
char str[32] = "";
String StrUID;

// Setup
void setup()
{
  Serial.begin(115200);

  // Initialize buzzer
  pinMode(buzzer, OUTPUT);

  // Initialize SPI and RC522
  SPI.begin();
  mfrc522.PCD_Init();

  // Initialize LCD
  lcd.init();
  lcd.backlight();
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(" Connecting to  ");
  lcd.setCursor(0, 1);
  lcd.print("    WiFi...     ");
  delay(500);

  // Connect to WiFi router
  WiFi.begin(ssid, password);
  Serial.println("");

  // Turn on LED until connected to WiFi
  pinMode(ON_Board_LED, OUTPUT);
  digitalWrite(ON_Board_LED, HIGH);
  Serial.print("Connecting...");

  // Wait for connection
  while (WiFi.status() != WL_CONNECTED)
  {
    Serial.print(".");
    // Blink while connecting to the WiFi
    digitalWrite(ON_Board_LED, LOW);
    delay(250);
    digitalWrite(ON_Board_LED, HIGH);
    delay(250);
  }

  // Display connection
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Connected to: ");
  lcd.setCursor(0, 1);
  lcd.print(ssid);
  delay(1000);

  // Turn off LED when connected
  digitalWrite(ON_Board_LED, HIGH);

  // Display WiFi details
  Serial.println("");
  Serial.print("Successfully connected to : ");
  Serial.println(ssid);
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
  Serial.println(destinationUrl);
  Serial.println("");
  Serial.println(title);
  Serial.println("Tap NFC card...");
  Serial.println("");

  // Set initial LCD display
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(title);
  lcd.setCursor(0, 1);
  lcd.print("Tap NFC Card...");
}

// Loop
void loop()
{
  // Read NFC card
  readsuccess = getid();

  if (readsuccess)
  {
    // START TIME
    unsigned long startTime = millis();

    digitalWrite(ON_Board_LED, LOW);

    // Declare object of class HTTPClient
    HTTPClient http;
    String UIDresultSend, postData;
    UIDresultSend = StrUID;

    // Send UID in post data
    postData = "UIDresult=" + UIDresultSend + "&room=" + room;

    // Specify request destination
    http.setTimeout(10000);
    http.begin(destinationUrl);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    // Send request and get response
    int httpCode = http.POST(postData);
    String payload = http.getString();

    // Print http details
    Serial.println(UIDresultSend);
    Serial.println(httpCode);
    Serial.println(payload);
    Serial.println("");

    // Parse JSON data
    DynamicJsonDocument doc(1024);
    DeserializationError error = deserializeJson(doc, payload);

    if (error)
    {
      Serial.print("Failed to parse JSON: ");
      Serial.println(error.c_str());
      return;
    }

    // Extract data from JSON
    String name = doc["studentData"]["last_name"];
    String studentNumber = doc["studentData"]["id_number"];
    String nfcUid = doc["studentData"]["nfc_uid"];
    String status = doc["studentData"]["status"];

    if (payload.indexOf("\"status\":\"Present\"") != -1)
    {
      status = "Present";
    }
    else if (payload.indexOf("\"status\":\"Late\"") != -1)
    {
      status = "Late";
    }
    else if (payload.indexOf("\"status\":\"Already recorded\"") != -1)
    {
      status = "Already recorded";
    }
    else
    {
      status = "No schedule";
    }
    
    // END TIME
    unsigned long endTime = millis();
    unsigned long elapsedTime = endTime - startTime;

    // Print the time passed between start time and end time
    Serial.print("Time elapsed: ");
    Serial.print(elapsedTime);
    Serial.println(" ms");

    // If no student found
    if (name == "none" && studentNumber == "none")
    {
      noStudentFound(UIDresultSend);
    }
    else
    {
      Serial.println(status);
      displayStudentInfo(name, studentNumber);
      if (status == "No schedule")
      {
        noSchedule();
      }
      else if (status == "Present")
      {
        present();
      }
      else if (status == "Late")
      {
        late();
      }
      else if (status == "Already recorded")
      {
        alreadyRecorded();
      }
    }

    // Reset LCD
    resetLCD(title);

    // Close http connection
    http.end();
    delay(1000);
    digitalWrite(ON_Board_LED, HIGH);
  }
}

void scrollText(int row, String message, int delayTime)
{
  for (int i = 0; i < LCD_COLUMNS; i++)
  {
    message = " " + message;
  }
  message = message + " ";
  for (int pos = 0; pos < message.length(); pos++)
  {
    lcd.setCursor(0, row);
    lcd.print(message.substring(pos, pos + LCD_COLUMNS));
    delay(delayTime);
  }
}

int getid()
{
  if (!mfrc522.PICC_IsNewCardPresent())
  {
    return 0;
  }
  if (!mfrc522.PICC_ReadCardSerial())
  {
    return 0;
  }

  Serial.print("THE UID OF THE SCANNED CARD IS : ");

  for (int i = 0; i < 4; i++)
  {
    readcard[i] = mfrc522.uid.uidByte[i];
    array_to_string(readcard, 4, str);
    StrUID = str;
  }
  mfrc522.PICC_HaltA();
  return 1;
}

void array_to_string(byte array[], unsigned int len, char buffer[])
{
  for (unsigned int i = 0; i < len; i++)
  {
    byte nib1 = (array[i] >> 4) & 0x0F;
    byte nib2 = (array[i] >> 0) & 0x0F;
    buffer[i * 2 + 0] = nib1 < 0xA ? '0' + nib1 : 'A' + nib1 - 0xA;
    buffer[i * 2 + 1] = nib2 < 0xA ? '0' + nib2 : 'A' + nib2 - 0xA;
  }
  buffer[len * 2] = '\0';
}

// LCD Responses
void noStudentFound(String UIDResultSend)
{
  Serial.println("No student found");

  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("    NFC card    ");
  lcd.setCursor(0, 1);
  lcd.print(" not registered ");

  digitalWrite(buzzer, HIGH);
  delay(750);
  digitalWrite(buzzer, LOW);

  String UIDresultDisplay = "    " + UIDResultSend + "    ";
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("      UID:      ");
  lcd.setCursor(0, 1);
  lcd.print(UIDresultDisplay);

  delay(3000);
}

void displayStudentInfo(String name, String studentNumber)
{
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(name);
  lcd.setCursor(0, 1);
  lcd.print(studentNumber);

  digitalWrite(buzzer, HIGH);
  delay(100);
  digitalWrite(buzzer, LOW);
  delay(650);

  lcd.clear();
}

void noSchedule()
{
  lcd.setCursor(0, 0);
  lcd.print("  No schedule   ");

  digitalWrite(buzzer, HIGH);
  delay(500);
  digitalWrite(buzzer, LOW);
  delay(250);
}

void present()
{
  lcd.setCursor(0, 0);
  lcd.print("    Recorded    ");

  digitalWrite(buzzer, HIGH);
  delay(100);
  digitalWrite(buzzer, LOW);
  delay(650);
}

void late()
{
  lcd.setCursor(0, 0);
  lcd.print("    Recorded    ");
  lcd.setCursor(0, 1);

  digitalWrite(buzzer, HIGH);
  delay(100);
  digitalWrite(buzzer, LOW);
  delay(100);
  digitalWrite(buzzer, HIGH);
  delay(100);
  digitalWrite(buzzer, LOW);
  delay(100);
  digitalWrite(buzzer, HIGH);
  delay(100);
  digitalWrite(buzzer, LOW);
  delay(250);
}

void alreadyRecorded()
{
  lcd.setCursor(0, 0);
  lcd.print("   Attendance   ");
  lcd.setCursor(0, 1);
  lcd.print("Already recorded");

  digitalWrite(buzzer, HIGH);
  delay(100);
  digitalWrite(buzzer, LOW);
  delay(100);
  digitalWrite(buzzer, HIGH);
  delay(100);
  digitalWrite(buzzer, LOW);
  delay(450);
}

void resetLCD(String title)
{
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(title);
  lcd.setCursor(0, 1);
  lcd.print("Tap NFC card...");
}