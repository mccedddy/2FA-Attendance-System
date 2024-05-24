# Execute in terminal: python distribute_uid.py "CPE 4-6"

import os
import sys
from dotenv import load_dotenv
import mysql.connector

load_dotenv()

db_user = os.getenv('DB_USER')
db_password = os.getenv('DB_PASSWORD')
db_host = os.getenv('DB_HOST')
db_name = os.getenv('DB_NAME')

db_config = {
  'user': db_user,
  'password': db_password,
  'host': db_host,
  'database': db_name
}

uid_list = [
    (0, '1330CC27'),
    (1, 'E3870828'),
    (2, 'A376B627'),
    (3, 'A3D01C2A'),
    (4, 'E3D04628'),
    (5, '2311B927'),
    (6, '7332672C'),
    (7, '731DC427'),
    (8, '63C08129'),
    (9, '03B9D527'),
    (10, '037FD327'),
    (11, '936A3F2A'),
    (12, '53CCE527'),
    (13, 'D3802C2A'),
    (14, '4374E127'),
    (15, '53F5D827'),
    (16, '5356252A'),
    (17, 'E3712F2A'),
    (18, '7338142A'),
    (19, '53790A2A'),
    (20, 'D3EB132A'),
    (21, '03536527'),
    (22, '8390AF27'),
    (23, 'C3DD6C28'),
    (24, 'A3BCA428'),
    (25, 'B3FE4328'),
    (26, '03DFE627'),
    (27, '93A6E027'),
    (28, '43D4292A'),
    (29, '738AE427'),
    (30, 'D340342A'),
    (31, '63C4E427'),
    (32, '43B4E527'),
    (33, '83955929'),
    (34, '73DD272A'),
    (35, '731DD627'),
    (36, '8373D127'),
    (37, '2327132A'),
    (38, 'C3D91C2A'),
    (39, 'C30B9829'),
    (40, '43041D2A'),
    (41, '93580728'),
    (42, 'C39AC327'),
    (43, 'B321302A'),
    (44, 'D3D7CC26'),
    (45, '6389B827'),
    (46, '03380828'),
    (47, '735FEC27'),
    (48, '9313D926')
]

def resetUID(cursor, conn):
  reset_sql = "UPDATE students SET nfc_uid = '00000000'"
  cursor.execute(reset_sql)
  conn.commit()

def distributeUID(target_section, cursor, conn):
  distribute_sql = "UPDATE students SET nfc_uid = %s WHERE id_number = %s"
  section_filter_sql = "SELECT id_number FROM students WHERE section = %s"
  cursor.execute(section_filter_sql, (target_section,))
  students_in_section = cursor.fetchall()
  for index, uid in uid_list:
    if index < len(students_in_section):
      student_id = students_in_section[index][0]
      cursor.execute(distribute_sql, (uid, student_id))
  conn.commit()


# Main function
def main():
  try:
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor()

    target_section = sys.argv[1] if len(sys.argv) > 1 else ''

    if target_section != '':
      resetUID(cursor, conn)
      distributeUID(target_section, cursor, conn)
      print(f"UIDs distributed to section: {target_section}")
    else:
      print("No section selected")
    
  except Exception as e:
    print(f"Error: {e}")
  finally:
    if cursor:
      cursor.close()
    if conn and conn.is_connected():
      conn.close()

if __name__ == "__main__":
    main()