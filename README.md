moodle-assign-sketchfab
=======================


A Sketchfab (http://www.sketchfab.com) submission plugin for Moodle 2.7+, allowing instructors to set 3D assignments with target polycounts, texture/material counts (and optionally supporting non-power-of-two textures).

## Use case
- Teacher sets an assignment, and enables both file uploading and Sketchfab integration for that assignment
- Student submits a .zip with their model and textures in it, along with their API key (not saved anywhere in Moodle for security reasons)
- Moodle kicks off a request to Sketchfab, and stores the resulting UID in its database
- Teacher now has:
 - a local copy of the student's .zip to run through diff/plagiarism tools
 - an embedded Sketchfab viewer on the view submission page to visually inspect the model and check it against assessment criteria without loading up a DCC app

## Testing instructions
- Install the plugin by creating a new folder in /mod/assign/submission, called "sketchfab" and putting the contents of this repository into it.
- As a teacher, create an assignment.
- Enable both file uploading and Sketchfab integration for the assignment.
-- This plugin requires that file uploading be enabled.
- Note that there is a new section of options for the assignment, labeled as 3D criteria. Leave them alone for now.
- Create an account at Sketchfab (http://www.sketchfab.com) and make a note of your API key (available at https://sketchfab.com/settings/password once you've logged in).
- As a student, submit the attached file, along with your API key.
 - The API key is never stored, as storing it would give the educational institution full read/write access over the user's Sketchfab account.
- As a teacher, view the student's submission. Observe that there is now a section labeled Sketchfab submissions that is expandable; expand it to show the viewer.
- Edit the settings for the assignment and turn on texture size restrictions. Set the texture size to be 512.
- View the submission again. Observe that the plugin has detected that the student's submission has one or more textures above the size restriction.
- Edit the settings for the assignment and turn on polygon count restrictions. Set the polycount to be 200.
- View the submission again. Observe that the plugin has detected that the student's submission is well within the range required.
