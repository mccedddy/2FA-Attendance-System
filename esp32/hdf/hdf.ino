// HDF Verification

// WiFi config
// const char* ssid = "TP-Link_95A7";
// const char* password = "98568758";
// const char* ssid = "TP-Link_12B6";
// const char* password = "66303336";
// const char* ssid = "JAM";
// const char* password = "C3dricJ0yce";
const char* ssid = "McDonald's Wi-Fi";
const char* password = "123abc123";

// Server config
// const String serverIP = "192.168.0.134"; // A7
const String serverIP = "192.168.166.227"; // McDonald's WiFi
String phpScript = "PUP-HDF-Attendance-System/services/verify_hdf.php";
String destinationUrl = "http://" + serverIP + "/" + phpScript;

// Device config
const String title = "HDF Verification";

// WiFi libraries
#include <WiFi.h>
#include <HTTPClient.h>

// SPI and MFRC522 libraries
#include <SPI.h>
#include <MFRC522.h>

// Wire for I2C and LCD libraries
#include <Wire.h> 
#include <LiquidCrystal_I2C.h> 

// JSON Library
#include <ArduinoJson.h>

// LCD Config
#define LCD_ADDRESS 0x27
#define LCD_COLUMNS 16
#define LCD_ROWS 2
LiquidCrystal_I2C lcd(LCD_ADDRESS, LCD_COLUMNS, LCD_ROWS);

// RC522 Config
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
void setup() {
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
  pinMode(ON_Board_LED,OUTPUT); 
  digitalWrite(ON_Board_LED, HIGH); 
  Serial.print("Connecting");

  // Wait for connection
  while (WiFi.status() != WL_CONNECTED) {
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
void loop() {
  // Read NFC card
  readsuccess = getid();
 
  if(readsuccess) {  
    digitalWrite(ON_Board_LED, LOW);

    // Declare object of class HTTPClient
    HTTPClient http;    
    String UIDresultSend, postData;
    UIDresultSend = StrUID;
   
    // Send UID in post data
    postData = "UIDresult=" + UIDresultSend;
  
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

    if (error) {
      Serial.print("Failed to parse JSON: ");
      Serial.println(error.c_str());
      return;
    }

    // Extract data from JSON
    String name = doc["studentData"]["last_name"];
    String studentNumber = doc["studentData"]["student_number"];
    String nfcUid = doc["studentData"]["nfc_uid"];
    int score = 100;

    if (studentNumber == "none") {
      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("    NFC card    ");
      lcd.setCursor(0, 1);
      lcd.print(" Not registered ");

      digitalWrite(buzzer, HIGH); delay(750); digitalWrite(buzzer, LOW);

    } else {
      // Extract the score value
      int scoreIndex = payload.indexOf("\"score\":");
      if (scoreIndex != -1) {
        String scoreSubstring = payload.substring(scoreIndex + 8, scoreIndex + 11);

        if (scoreSubstring == "100") {
          score = scoreSubstring.toInt();
        } else {
          scoreSubstring = payload.substring(scoreIndex + 8, scoreIndex + 10);
          score = scoreSubstring.toInt();
        }
        Serial.println("Score: " + String(score));

      } else {
        Serial.println("Score not found in payload.");
      }

      // Display student info
      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print(name);
      lcd.setCursor(0, 1);
      lcd.print(studentNumber);

      digitalWrite(buzzer, HIGH); delay(100); digitalWrite(buzzer, LOW);
      delay(650);

      // Display score
      lcd.clear();
      if (score < 0) {
        lcd.setCursor(0, 0);
        lcd.print("No HDF recorded");
        lcd.setCursor(0, 1);
        lcd.print(" Access denied  ");

        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);

      } else if (score < 25 && score >= 0) {
        lcd.setCursor(0, 0);
        lcd.print(" HDF Score: " + String(score));
        lcd.setCursor(0, 1);
        lcd.print(" Access denied  ");

        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        
      } else if (score < 50 && score >= 25) {
        lcd.setCursor(0, 0);
        lcd.print(" HDF Score: " + String(score));
        lcd.setCursor(0, 1);
        lcd.print(" Access denied  ");

        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);

      } else if (score < 75 && score >= 50) {
        lcd.setCursor(0, 0);
        lcd.print(" HDF Score: " + String(score));
        lcd.setCursor(0, 1);
        lcd.print(" Access denied  ");

        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);
        digitalWrite(buzzer, HIGH); delay(500); digitalWrite(buzzer, LOW); delay(500);

      } else if (score < 100 && score >= 75) {
        lcd.setCursor(0, 0);
        lcd.print(" HDF Score: " + String(score));
        lcd.setCursor(0, 1);
        lcd.print(" Access granted");

        digitalWrite(buzzer, HIGH); delay(100); digitalWrite(buzzer, LOW);
        delay(650);
        
      } else {
        lcd.setCursor(0, 0);
        lcd.print(" HDF Score: 100 ");
        lcd.setCursor(0, 1);
        lcd.print(" Access granted");
        
        digitalWrite(buzzer, HIGH); delay(100); digitalWrite(buzzer, LOW);
        delay(650);
      }
    }

    // Reset LCD
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print(title);
    lcd.setCursor(0, 1);
    lcd.print("Tap NFC card...");
    
    // Close http connection
    http.end();
    delay(1000);
    digitalWrite(ON_Board_LED, HIGH);
  }
}

void scrollText(int row, String message, int delayTime) {
  for (int i=0; i < LCD_COLUMNS; i++) {
    message = " " + message;  
  } 
  message = message + " "; 
  for (int pos = 0; pos < message.length(); pos++) {
    lcd.setCursor(0, row);
    lcd.print(message.substring(pos, pos + LCD_COLUMNS));
    delay(delayTime);
  }
}

int getid() {  
  if(!mfrc522.PICC_IsNewCardPresent()) {
    return 0;
  }
  if(!mfrc522.PICC_ReadCardSerial()) {
    return 0;
  }
  
  Serial.print("THE UID OF THE SCANNED CARD IS : ");
  
  for(int i=0;i<4;i++){
    readcard[i]=mfrc522.uid.uidByte[i]; 
    array_to_string(readcard, 4, str);
    StrUID = str;
  }
  mfrc522.PICC_HaltA();
  return 1;
}

void array_to_string(byte array[], unsigned int len, char buffer[]) {
    for (unsigned int i = 0; i < len; i++)
    {
        byte nib1 = (array[i] >> 4) & 0x0F;
        byte nib2 = (array[i] >> 0) & 0x0F;
        buffer[i*2+0] = nib1  < 0xA ? '0' + nib1  : 'A' + nib1  - 0xA;
        buffer[i*2+1] = nib2  < 0xA ? '0' + nib2  : 'A' + nib2  - 0xA;
    }
    buffer[len*2] = '\0';
}
