ADMIN CLASSLIST

1. upload features should:

- insert data from features_all.csv to db. (already working)
- if student has existing features on db, update. else, insert to features table. (not started) (see ../includes/upload_features.php)

2. clear data should: (not started)

- delete student features on the db (features table).
- delete student folder on captures (../captures/2020-XXXXX-MN-0).
- delete student features on features_all.csv (../captures/features_all.csv)

3. registered column on classlist should be reflected on whether student has features on features table. 

4. add alerts:

- images processed
- featuress uploaded
- cleared features

5. what if: process images + upload features in 1 button. After processing images, automatically upload features to db.

6. what if: after uploading, delete features_all.csv and student folders in ../captures.
