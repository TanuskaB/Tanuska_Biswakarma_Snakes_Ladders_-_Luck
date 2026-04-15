# Tanuska_Biswakarma_Snakes_Ladders_-_Luck
Snakes, Ladders &amp; Luck is a PHP-powered digital board game inspired by classic titles like Snakes &amp; Ladders. Players roll a virtual die using server-side logic to move across a dynamically generated 100-cell grid.

# Project Title & Description
Snakes, Ladders & Luck is a digital board game built using PHP, based on the classic Snakes and
Ladders concept. Players roll a virtual dice to move across a 100-cell board, trying to reach the final
tile first while avoiding snakes and climbing ladders. The game delivers a fun and simple
entertainment experience, giving users a mix of luck, suspense, and friendly competition.
What makes this game unique is that it is fully powered by PHP on the server side. It includes
multiple difficulty levels that change the board layout, hazards, and bonus tiles. The game also tracks
player progress, turn order, and dice history using sessions, so the game continues even after
refreshing the page. Smooth animations and highlighted player positions make it more interactive
and visually engaging.

# Usage Guide
* Create a new account using the registration page
* Log in with your credentials to access the game
* Navigate to the game page and start playing
* Scores are recorded and stored
* View top scores and rankings of all users

# Deployment Instructions
This project is hosted on the Georgia State CODD server. The files were uploaded using FileZilla via FTP.
Follow these steps to deploy and run the project.

1. Clone the Repository
Clone the project to your local machine:
git clone https://github.com/TanuskaB/Tanuska_Biswakarma_Snakes_Ladders_-_Luck.git

2. Connect to the CODD Server
Open FileZilla and connect using your GSU credentials.
Typical connection settings:
Host: codd.cs.gsu.edu
Username: your GSU username
Password: your GSU password
Port: 22
Once connected, navigate to your public web directory.

3. Upload the Project Files
Upload the entire project folder to:
public_html/wp/project/
Example structure:
```
public_html/
└── wp/
    └── project/
        └── Snakes_Ladder_Luck/
            ├── index.php
            ├── login.php
            ├── register.php
            ├── game.php
            ├── howtoplay.php
            ├── leaderboard.php
            ├── logout.php
            ├── style.css
            ├── config.php
            └── functions.php
```
                
4. Access the Project
After uploading the files, open the project in a browser using:
https://codd.cs.gsu.edu/~username/wp/project/project-folder/index.php
Example:
https://codd.cs.gsu.edu/~cmajor7/wp/project/Snakes_Ladders_Luck/index.php
5. Run the Game
Register a new account
Log in
Start the game
Roll the dice and move across the board

# Team Members
| Name | Student ID | Primary Contribution |
| -------- | -------- | -------- |
| Tanuska Biswakarma | 002682850 | Login/Register system, session handling, backend PHP logic |
| Caira Major | 002681888 | Frontend UI design, CSS styling, navigation flow, game interface |

# Live CODD URL
https://codd.cs.gsu.edu/~tbiswakarma1/wp/project/Tanuska_Biswakarma_Snakes_Ladders_Luck/index.php