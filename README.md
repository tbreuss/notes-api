# Notes Management Tool – REST-API

The REST-API for the Notes Management Tool.

Note: This is the API only.
You need the appropriate client which is hosted at <https://github.com/tbreuss/notes-client>.

## Install

    git clone https://github.com/tbreuss/notes-api.git
    cd notes-api
    composer install

## Create/import database

Create a database at your hosting provider and import `config\mysql-dump.sql`.

## Config

Copy configuration files:

    cd config
    cp db.dist.php db.php
    cp params.dist.php params.php

Edit both files according to your config settings. 

## Run

    cd notes-server
    php yii serve -p 8888

Open your webbrowser <http://localhost:8888/>

You should see:

    <response>
        <title>REST-API for Notes Management Tool</title>
        <info>You need an appropriate client to access this API</info>
        <github>https://github.com/tbreuss/notes-client</github>
        <url>https://notes.tebe.ch</url>
    </response>

## Build

    composer build

Build a zip archive for production. Needs globally installed `git` and `composer` and an existing `config/prod.env.php`.

          
## Endpoints

To be done.

### GET v1/ping

### POST v1/login

### GET v1/articles/<id:\d+>

### PUT v1/articles/<id:\d+>

### DELETE v1/articles/<id:\d+>

### GET v1/articles

### POST v1/articles

### GET v1/articles/latest

### GET v1/articles/liked

### GET v1/articles/modified

### GET v1/articles/popular

### POST v1/articles/upload

### GET v1/users/<id:\d+>

### GET v1/users

### GET v1/tags/<id:\d+>

### GET v1/tags

### GET v1/tags/selected

## cURL calls

To be done.

Post login
    
    curl -i --header "Content-Type: application/json" --request POST --data '{"username":"xyz","password":"xyz"}' http://localhost:8888/v1/login
    curl -i --header "Content-Type: application/json" --request OPTIONS --data '{"username":"xyz","password":"xyz"}' http://localhost:8888/v1/login
    curl -i -X OPTIONS http://localhost:8888/ --header "Content-Type: application/json"
