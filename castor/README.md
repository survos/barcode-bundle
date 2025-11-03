# Scaffold an application using Castor

This directory contains a castor file and some code snippets to build a demo that uses survos/barcode-bundle

Create a Symfony project and then get this file

wget ... | bash
castor list

You can run each step individually, or build a working demo

castor build

New chat

I'm going to use castor task runner to scaffold an application that demonstrates how to use my Symfony bundles.



The workflow I want is



symfony new...

wget/curl the castor file from github

castor build (run composer req my-bundle, creates a controller and twig file, create a console command to import some data, create an entity)



Each of those steps can be run individually.



I have a working castor file, my first question is where in the bundle should the castor file and artifacts(need a better word.  inputs?) go?  I'm thinking castor



tree castor/

castor/

├── castor.php

├── README.md

├── src

│   ├── Command

│   │   └── ImportProductsCommmand.php

│   └── Entity

│       └── Product.php

└── templates

    └── products.html.twig



Then the castor file would copy these files into the right location to scaffold the app.  Great way to make sure everything still works in the upcoming Symfony 8.0





Need a coding agent?
Best-in-class coding agent
Switch to Claude Code
