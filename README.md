# LTI Filter #

A Moodle text filter that enables embedding LTI (Learning Tools Interoperability) external tools using simple tag syntax within course content.

## Description ##

The LTI Filter allows instructors to easily embed configured LTI external tools (such as Mediasite and Padlet) in course content using a simple markup syntax. Rather than manually creating and linking to individual LTI activities, instructors can use tags like `{lti:ActivityName}` or `{mediasite:ActivityName}` within text to automatically embed the corresponding LTI activity. The filter automatically matches tag names to existing LTI activities in the course and generates the appropriate embedded content (iframe). If a tag cannot be matched to an activity, instructors are notified with a warning (students do not see this message). Custom prefixes can be configured to support additional resource types beyond the default LTI and common integrations like Mediasite and Padlet. This plugin was inspired by Filter H5P (https://moodle.org/plugins/filter_h5p).

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/filter/lti

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

2022 Andrew Rowatt <A.J.Rowatt@massey.ac.nz>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
