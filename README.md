# notes-server-yii2

Start webserver:

    php yii serve -p 8888
    
Post login
    
    curl -i --header "Content-Type: application/json" --request POST --data '{"username":"xyz","password":"xyz"}' http://localhost:8080/login
    curl -i --header "Content-Type: application/json" --request OPTIONS --data '{"username":"xyz","password":"xyz"}' http://localhost:8080/login
    curl -i -X OPTIONS http://localhost:8888/ --header "Content-Type: application/json"
      
## Endpoints

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
