# Mouse tracker v0.1

*Please beware: **This project is obsolete**, and it is not being maintained. Use at your own risk.*

## Description

MouseTracker is a small HTML+Javascript snippet that can be added to a webpage to track visitors' mouse movements and clicks.  The stream of data is stored in a MySql database to allow for later analysis/processing.

There are two parts: tracker and player.  Both parts use the same database, but they should be used separately.  Typically, the tracker is modified and put as part of a public website; while the player is put in a place with restricted access.

The tracker is provided as a template, so you could put it along with a website you want to track.  When installed within a page, all objects that have a class 'trackable' will be tracked: that is, all mouse enters, mouse leaves, and clicks on those objects will be logged, and the whole record will be stored in a database.  Also, all movements within the browser viewport are recorded.

The tracker makes use of HTML and Javascript (JQuery) only (no PHP); however, it invokes a PHP script to store all mouse tracking data in a MySql database.

The player is a PHP + MySql application that lets you replay mouse movements and clicks on a page.


## Dependencies

The tracker makes use of JQuery, 2.0.3 or higher. Download the last version from http://jquery.com/download.

The player makes use of Smarty templates. Download the last version from http://www.smarty.net/download.

## Installation

To install:

1. Unzip the MouseTracking file somewhere accessible by a web browser, e.g., /home/johndoe/public_html.
1. Create a database in MySql, create a user for the application, and grant all privileges on the database to that user:

		mysql -u root -p
		> create database mt;
		> grant all privileges on mt.* to mt@localhost identified by 'mt';

1. Modify the file 'common/db.php' to specify the db name, username and password set in the previous step.
1. Create the tables needed:

		% mysql -u mt -p < /home/johndoe/public_html/common/mt-db-create.sql

1. Point your browser to the tracker: http://localhost/~johndoe/tracker/index.html. You will see an example of a page being tracked.  Move your mouse and click the boxes in order (i.e., first box 'one', then box 'two', then box 'three').  This will trigger the submission of data to the store script. You may specify whatever set of actions to trigger the store script, or you may invoke a script directly to do that.

1. Now point your browser to the player: http://localhost/~johndoe/player/index.php. You should see a toolbar with a button to process one pending track. Click on it.
1. The page should reload, and you should see a table with one row corresponding to the only track session you just created. Click on the 'Play' button. You should see a new page, displaying the recorded session.

## Privacy disclaimer

Although there's nothing inherently wrong with tracking people's mouse movements (I did it as part of my doctoral thesis, and all my research was supervised and approved by my university's IRB), please be aware that tracking people's mouse movements and clicks can be considered as privacy invasive by your visitors.  If you use this package, or any other that allows you to track your visitors' whereabouts, be transparent and tell your users what you're doing, and why you're doing it.


## License

MouseTracker (c)2014 Cristian Bravo-Lillo

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
