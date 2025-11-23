-- SQL Script to Prevent Duplicate Bookings
-- This script adds a unique constraint to prevent users from booking the same package multiple times

-- Add unique constraint to tblbooking table
-- This ensures that a user cannot book the same package more than once
ALTER TABLE tblbooking 
ADD CONSTRAINT unique_user_package 
UNIQUE (userid, package_id);

-- If you want to remove existing duplicate bookings first, run this:
-- DELETE b1 FROM tblbooking b1
-- INNER JOIN tblbooking b2 
-- WHERE b1.id > b2.id 
-- AND b1.userid = b2.userid 
-- AND b1.package_id = b2.package_id;

-- Note: Run the DELETE statement first if you have existing duplicate bookings
-- Then run the ALTER TABLE statement to add the constraint
