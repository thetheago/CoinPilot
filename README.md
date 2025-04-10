# 👋 Olá

> Primeiro obrigado pela oportunidade, espero que goste do projeto!

## 🚀 Tecnologias Utilizadas

- PHP 8.x
- [Laravel 12](https://laravel.com/)
- Composer
- Docker & compose
- Redis
- (Outras tecnologias/libs: Swagger, Phpunit, LogViewer, Horizon..)

### 🐨 Pré-requisitos

- docker compose

## ⚙️ Como Executar o Projeto
- Abra o terminal no diretório do docker-compose.yml
- Rode ```docker compose up```
- As rotas estão disponiveis em http://localhost:8081/api/
- http://localhost:8081/log-viewer para monitoramento (observabilidade)
- http://localhost:8081/horizon para monitorar os jobs

## 🐦 Documentação da API
- Acesse http://localhost:8081/api/documentation

## 🗺️ Planejamento
[https://miro.com/app/board/uXjVIG4qQQ8=/](https://miro.com/app/board/uXjVIG4qQQ8=/?share_link_id=416542207277)
![2025-04-10_11-02](https://github.com/user-attachments/assets/9aaaba94-cb58-4307-a195-576e22e22b51)


### 📄 Referências
#### [Miro | Planejamento]
https://medium.com/@shek.up/domain-driven-design-for-banking-application-d6279ecdaf2  
https://substack.wasteman.codes/p/engineering-principles-and-best-practices  
https://youtu.be/f4GolIiNIvc?si=vZjRSSfduY3poki7
https://medium.com/@guilhermeguini/optimistic-lock-965d78a56140
https://github.com/thetheago/bankAPI/tree/main   
https://github.com/thetheago/Finnance-backend-challange
https://martinfowler.com/eaaDev/EventSourcing.html

#### [branchs starting-database-structure | create-github-actions-on-trigger-main]
https://github.com/symfony/demo/blob/main/phpunit.xml.dist
https://gist.github.com/roberto-butti/d490a8eb02bf4a540f7fd1715df18970

#### [tests]
https://youtu.be/x2EMBvG8P3M?si=EZnoMF3herqnypQ3
https://darkghosthunter.medium.com/php-10-tips-to-use-for-mockery-33673ba01321

#### [branch observability]
https://www.youtube.com/watch?v=4i6iASabll0

#### [branch api-doc]
https://medium.com/inside-picpay/documenta%C3%A7%C3%A3o-de-apis-voc%C3%AA-conhece-o-swagger-fd8b403d27ed

#### [branch queue-structure]
https://medium.com/%40osvaldogarcia_67748/integrating-laravel-and-rabbitmq-with-docker-and-php-8-1-1718a2c41e83
https://medium.com/%40divyaaditya/two-laravel-applications-rabbitmq-using-docker-compose-producer-consumer-setup-c8216c9ea466
https://medium.com/@osvaldogarcia_67748/integrating-laravel-and-rabbitmq-with-docker-and-php-8-1-1718a2c41e83
https://laravel.com/docs/11.x/redis

#### [branch events-sourcing-transfer-flux]
https://github.com/thetheago/BehavioralPatterns/blob/main/iterator/src/BudgetsList.php (Iterator pattern)
