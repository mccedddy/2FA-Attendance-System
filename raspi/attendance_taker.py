import dlib
import numpy as np
import cv2
import os
import pandas as pd
import time
import logging
from datetime import datetime, timedelta
import mysql.connector

db_host = 'localhost'
db_user = 'puphas'
db_password = 'Puphas-2024'
db_name = 'puphas'

# Dlib  / Use frontal face detector of Dlib
detector = dlib.get_frontal_face_detector()

# Dlib landmark / Get face landmarks
predictor = dlib.shape_predictor('data/data_dlib/shape_predictor_68_face_landmarks.dat')

# Dlib Resnet Use Dlib resnet50 model to get 128D face descriptor
face_reco_model = dlib.face_recognition_model_v1("data/data_dlib/dlib_face_recognition_resnet_model_v1.dat")

try:
    conn = mysql.connector.connect(
        host=db_host,
        user=db_user,
        password=db_password,
        database=db_name
    )

    if conn.is_connected():
        print("Successfully connected to the database")
        cursor = conn.cursor()
        cursor.execute("SELECT DATABASE();")
        record = cursor.fetchone()
        print("Connected to database:", record)
except mysql.connector.Error as err:
    print(f"Error: {err}")

class Face_Recognizer:
    def __init__(self):
        self.font = cv2.FONT_ITALIC

        # FPS
        self.frame_time = 0
        self.frame_start_time = 0
        self.fps = 0
        self.fps_show = 0
        self.start_time = time.time()

        # cnt for frame
        self.frame_cnt = 0

        #  Save the features of faces in the database
        self.face_features_known_list = []
        # / Save the name of faces in the database
        self.face_name_known_list = []

        #  List to save centroid positions of ROI in frame N-1 and N
        self.last_frame_face_centroid_list = []
        self.current_frame_face_centroid_list = []

        # List to save names of objects in frame N-1 and N
        self.last_frame_face_name_list = []
        self.current_frame_face_name_list = []

        #  cnt for faces in frame N-1 and N
        self.last_frame_face_cnt = 0
        self.current_frame_face_cnt = 0

        # Save the e-distance for faceX when recognizing
        self.current_frame_face_X_e_distance_list = []

        # Save the positions and names of current faces captured
        self.current_frame_face_position_list = []
        #  Save the features of people in current frame
        self.current_frame_face_feature_list = []

        # e distance between centroid of ROI in last and current frame
        self.last_current_frame_centroid_e_distance = 0

        #  Reclassify after 'reclassify_interval' frames
        self.reclassify_interval_cnt = 0
        self.reclassify_interval = 10

    #  "features_all.csv"  / Get known faces from "features_all.csv"
    def get_face_database(self):

        cursor.execute("SELECT * FROM features")
        rows = cursor.fetchall()

        if rows:
            path_features_known_csv = "data/features_all.csv"
            with open(path_features_known_csv, 'w') as file:
                for row in rows:
                    file.write(','.join(map(str, row)) + '\n')

            csv_rd = pd.read_csv(path_features_known_csv, header=None)
            for i in range(csv_rd.shape[0]):
                features_someone_arr = []
                self.face_name_known_list.append(csv_rd.iloc[i][0])
                for j in range(1, 129):
                    if csv_rd.iloc[i][j] == '':
                        features_someone_arr.append('0')
                    else:
                        features_someone_arr.append(csv_rd.iloc[i][j])
                self.face_features_known_list.append(features_someone_arr)
            logging.info("Faces in Database： %d", len(self.face_features_known_list))
            return 1
        else:
            logging.warning("'features_all.csv' not found!")
            logging.warning("Please run 'get_faces_from_camera.py' "
                            "and 'features_extraction_to_csv.py' before 'face_reco_from_camera.py'")
            return 0

    def update_fps(self):
        now = time.time()
        # Refresh fps per second
        if str(self.start_time).split(".")[0] != str(now).split(".")[0]:
            self.fps_show = self.fps
        self.start_time = now
        self.frame_time = now - self.frame_start_time
        self.fps = 1.0 / self.frame_time
        self.frame_start_time = now

    @staticmethod
    # / Compute the e-distance between two 128D features
    def return_euclidean_distance(feature_1, feature_2):
        feature_1 = np.array(feature_1, dtype=np.float64)
        feature_2 = np.array(feature_2, dtype=np.float64)
        dist = np.sqrt(np.sum(np.square(feature_1 - feature_2)))
        return dist

    # / Use centroid tracker to link face_x in current frame with person_x in last frame
    def centroid_tracker(self):
        for i in range(len(self.current_frame_face_centroid_list)):
            e_distance_current_frame_person_x_list = []
            #  For object 1 in current_frame, compute e-distance with object 1/2/3/4/... in last frame
            for j in range(len(self.last_frame_face_centroid_list)):
                self.last_current_frame_centroid_e_distance = self.return_euclidean_distance(
                    self.current_frame_face_centroid_list[i], self.last_frame_face_centroid_list[j])

                e_distance_current_frame_person_x_list.append(
                    self.last_current_frame_centroid_e_distance)

            last_frame_num = e_distance_current_frame_person_x_list.index(
                min(e_distance_current_frame_person_x_list))
            self.current_frame_face_name_list[i] = self.last_frame_face_name_list[last_frame_num]

    #  cv2 window / putText on cv2 window
    def draw_note(self, img_rd):
        # Get the dimensions of the image
        height, width = img_rd.shape[:2]

        # Text to be displayed
        text = "Attendance Verification"
        font_scale = 1.5
        font_thickness = 2

        # Calculate the size of the text
        text_size = cv2.getTextSize(text, self.font, font_scale, font_thickness)[0]

        # Calculate the position for the text to be centered
        text_x = (width - text_size[0]) // 2
        text_y = text_size[1] + 20  # Slightly below the top edge

        # Add a white rectangle as background
        rectangle_bgr = (255, 255, 255)
        top_left = (text_x - 10, text_y - text_size[1] - 10)
        bottom_right = (text_x + text_size[0] + 10, text_y + 10)
        cv2.rectangle(img_rd, top_left, bottom_right, rectangle_bgr, cv2.FILLED)

        # Add the text to the frame
        cv2.putText(img_rd, text, (text_x, text_y), self.font, font_scale, (0, 0, 0), font_thickness, cv2.LINE_AA)
    # insert data in database

    def attendance(self, id_number, img_rd):
        current_date = datetime.now().strftime('%Y-%m-%d')

        # Define the maximum allowed time difference (10 minutes)
        max_time_difference = timedelta(minutes=10)

        # Calculate the time threshold
        threshold_time = datetime.now() - max_time_difference

        # Convert threshold_time to string format for comparison in the query
        threshold_time_str = threshold_time.strftime('%Y-%m-%d %H:%M:%S')

        try:
            conn = mysql.connector.connect(
                host=db_host,
                user=db_user,
                password=db_password,
                database=db_name
            )

            if conn.is_connected():
                cursor = conn.cursor()
                cursor.execute("SELECT * FROM attendance WHERE id_number = %s AND date = %s AND time >= %s AND verified = 0", (id_number, current_date, threshold_time_str))
                existing_entry = cursor.fetchone()

                if existing_entry:
                    cursor.execute("UPDATE attendance SET verified = 1 WHERE id_number = %s AND date = %s",(id_number, current_date))
                    conn.commit()
                    print(f"{id_number} attendance verified for {current_date}.")
                    # Display prompt for 1 second
                    start_time = time.time()
                    while time.time() - start_time < 1:  # Display for 1 second
                        # Fill the entire frame with a black rectangle
                        img_rd = cv2.rectangle(img_rd, (0, 0), (img_rd.shape[1], img_rd.shape[0]), (0, 0, 0), -1)
                        
                        # Get the dimensions of the image
                        height, width = img_rd.shape[:2]

                        # Text to be displayed
                        text = "Attendance Verified"
                        font_scale = 1.5
                        font_thickness = 2

                        # Calculate the size of the text
                        text_size = cv2.getTextSize(text, self.font, font_scale, font_thickness)[0]

                        # Calculate the position for the text to be centered
                        text_x = (width - text_size[0]) // 2
                        text_y = (height + text_size[1]) // 2

                        # Add the text to the frame
                        img_rd = cv2.putText(img_rd, text, (text_x, text_y), self.font, font_scale, (0, 255, 0), font_thickness, cv2.LINE_AA)

                        cv2.imshow("camera", img_rd)
                        cv2.waitKey(1)  # Update display

        except mysql.connector.Error as err:
            print(f"Error: {err}")

        #  Face detection and recognition wit OT from input video stream
    def process(self, stream):
        # 1. Get faces known from "features.all.csv"
        if self.get_face_database():
            while stream.isOpened():
                self.frame_cnt += 1
                logging.debug("Frame " + str(self.frame_cnt) + " starts")
                flag, img_rd = stream.read()
                kk = cv2.waitKey(1)

                # 2. Detect faces for frame X
                faces = detector(img_rd, 0)

                # 3. Update cnt for faces in frames
                self.last_frame_face_cnt = self.current_frame_face_cnt
                self.current_frame_face_cnt = len(faces)

                # 4. Update the face name list in last frame
                self.last_frame_face_name_list = self.current_frame_face_name_list[:]

                # 5. Update frame centroid list
                self.last_frame_face_centroid_list = self.current_frame_face_centroid_list
                self.current_frame_face_centroid_list = []

                # 6.1 If cnt not changes
                if (self.current_frame_face_cnt == self.last_frame_face_cnt) and (
                        self.reclassify_interval_cnt != self.reclassify_interval):
                    logging.debug("scene 1: No face cnt changes in this frame!!!")

                    self.current_frame_face_position_list = []

                    if "unknown" in self.current_frame_face_name_list:
                        self.reclassify_interval_cnt += 1

                    if self.current_frame_face_cnt != 0:
                        for k, d in enumerate(faces):
                            self.current_frame_face_position_list.append(tuple(
                                [faces[k].left(), int(faces[k].bottom() + (faces[k].bottom() - faces[k].top()) / 4)]))
                            self.current_frame_face_centroid_list.append(
                                [int(faces[k].left() + faces[k].right()) / 2,
                                int(faces[k].top() + faces[k].bottom()) / 2])

                            img_rd = cv2.rectangle(img_rd,
                                                tuple([d.left(), d.top()]),
                                                tuple([d.right(), d.bottom()]),
                                                (255, 255, 255), 2)

                    # Multi-faces in the current frame, use centroid-tracker to track
                    if self.current_frame_face_cnt != 1:
                        self.centroid_tracker()

                    for i in range(self.current_frame_face_cnt):
                        # 6.2 Write names under ROI
                        img_rd = cv2.putText(img_rd, self.current_frame_face_name_list[i],
                                            self.current_frame_face_position_list[i], self.font, 0.8, (0, 255, 255), 1,
                                            cv2.LINE_AA)
                    self.draw_note(img_rd)

                # 6.2 If cnt of faces changes, 0->1 or 1->0 or ...
                else:
                    logging.debug("scene 2: Faces cnt changes in this frame")
                    self.current_frame_face_position_list = []
                    self.current_frame_face_X_e_distance_list = []
                    self.current_frame_face_feature_list = []
                    self.reclassify_interval_cnt = 0

                    # 6.2.1 Face cnt decreases: 1->0, 2->1, ...
                    if self.current_frame_face_cnt == 0:
                        logging.debug("No faces in this frame!!!")
                        # Clear the list of names and features
                        self.current_frame_face_name_list = []
                    # 6.2.2 Face cnt increase: 0->1, 0->2, ..., 1->2, ...
                    else:
                        logging.debug("Get faces in this frame and do face recognition")
                        self.current_frame_face_name_list = []
                        for i in range(len(faces)):
                            shape = predictor(img_rd, faces[i])
                            self.current_frame_face_feature_list.append(
                                face_reco_model.compute_face_descriptor(img_rd, shape))
                            self.current_frame_face_name_list.append("unknown")

                        # Traversal all the faces in the database
                        for k in range(len(faces)):
                            logging.debug("For face %d in the current frame:", k + 1)
                            self.current_frame_face_centroid_list.append(
                                [int(faces[k].left() + faces[k].right()) / 2,
                                int(faces[k].top() + faces[k].bottom()) / 2])

                            self.current_frame_face_X_e_distance_list = []

                            # Positions of faces captured
                            self.current_frame_face_position_list.append(tuple(
                                [faces[k].left(), int(faces[k].bottom() + (faces[k].bottom() - faces[k].top()) / 4)]))

                            # For every faces detected, compare the faces in the database
                            for i in range(len(self.face_features_known_list)):
                                if str(self.face_features_known_list[i][0]) != '0.0':
                                    e_distance_tmp = self.return_euclidean_distance(
                                        self.current_frame_face_feature_list[k],
                                        self.face_features_known_list[i])
                                    logging.debug("With person %d, the e-distance: %f", i + 1, e_distance_tmp)
                                    self.current_frame_face_X_e_distance_list.append(e_distance_tmp)
                                else:
                                    # person_X
                                    self.current_frame_face_X_e_distance_list.append(999999999)

                            # Find the one with the minimum e distance
                            similar_person_num = self.current_frame_face_X_e_distance_list.index(
                                min(self.current_frame_face_X_e_distance_list))

                            if min(self.current_frame_face_X_e_distance_list) < 0.4:
                                self.current_frame_face_name_list[k] = self.face_name_known_list[similar_person_num]
                                logging.debug("Face recognition result: %s",
                                            self.face_name_known_list[similar_person_num])

                                # Retrieve the ID number from the CSV file
                                id_number = self.face_name_known_list[similar_person_num]
                                self.attendance(id_number, img_rd)
                            else:
                                logging.debug("Face recognition result: Unknown person")

                        # Add note on cv2 window
                        self.draw_note(img_rd)

                # Press 'q' to exit
                if kk == ord('q'):
                    break

                self.update_fps()
                cv2.namedWindow("camera", 1)
                cv2.imshow("camera", img_rd)

                logging.debug("Frame ends\n\n")

    def run(self):
        cap = cv2.VideoCapture(0)  # Get video stream from camera
        self.process(cap)

        cap.release()
        cv2.destroyAllWindows()


def main():
    # logging.basicConfig(level=logging.DEBUG) # Set log level to 'logging.DEBUG' to print debug info of every frame
    logging.basicConfig(level=logging.INFO)
    Face_Recognizer_con = Face_Recognizer()
    Face_Recognizer_con.run()


if __name__ == '__main__':
    main()