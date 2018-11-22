# notes-server-yii2


Start webserver:

    vendor/bin/yii serve --docroot=./web
    
    
    
    

Post login
    
    curl -i --header "Content-Type: application/json" --request POST --data '{"username":"xyz","password":"xyz"}' http://localhost:8080/login
      