# ClubHub

### install composer packages
docker exec -it phpclub composer  install

### start node watsh
docker exec -it nodeclub sh
npm install
npm run watch

### migration bdd
docker exec -it phpclub php bin/console d:m:m
