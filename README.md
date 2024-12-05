# 2FA Attendance System #

This is a two-factor authentication attendance system that uses NFC and machine vision. It includes an NFC scanner to record attendance and a Raspberry Pi for facial recognition and attendance verification. Admins can register students and manage classes, while professors can view and analyze attendance data.

## Features ##

- **Two-Factor Authentication** - Combines NFC and facial recognition for secure attendance verification.
- **NFC-Based Attendance** - Students tap their NFC cards to record attendance.
- **Facial Recognition** - Ensures the identity of the student using a Raspberry Pi and a camera module.
- **Admin Features**
    - Register and manage student data.
    - Register student faces for facial recognition
    - Set up and manage class schedules and subjects.
- **Professor Features**
    - View and analyze attendance data in real-time.
    - Generate attendance reports.
    - Export and import classlist.

## Tech Stack ##
### Front-End ###
- **HTML**
- **CSS**
- **JavaScript**
  
### Back-End ###
- **PHP**
- **MySQL**
  
### Hardware ###
- **ESP32 Microcontroller with NFC scanner** - Handles NFC attendance recording.
- **Raspberry Pi with Camera Module** - Runs Python scripts for facial recognition and attendance verification.
  
### Other Languages ###
- **C++** - Used for the handling the recording of attendance using NFC scanner and ESP32 microcontroller
- **Python** - Runs scripts for facial recognition and attendance verification on Raspberry Pi
