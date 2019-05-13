## Laravel YouTube - 0.0.1

![Laravel YouTube](logo.png)

**Atention:** `Documentation in english please` [click for here](README-EN.md)

O **laravel-youtube** consome a API do **YouTube** e prove uma forma simples de 
gerar e manipular seus videos no YouTube. 

Também é capaz de devolver estatísticas, relatórios relacionados aos seus 
vídeos no YouTube e criar o **RTMP** e **chave de transmissão** para uma transmissão
 ao vivo.

## Compatibilidade

 PHP >= 7.0
 Laravel 5.x
 
 
 ## Instalação
 
Abra o arquivo `composer.json` e insira a seguinte instrução:
 
     "require": {
         "michael/laravel-youtube": "0.0.1"
     }
     
     
Após inserir no require a `Laravel YouTube`, você deverá executar o comando:

    composer update
    
    
Ou execute o comando:

    composer require michael/laravel-youtube
    

## Configuração do Service Provider

Abra o arquivo `config/app.php` e adicione no array `providers` a seguinte instrução:

```php
Laravel\Youtube\YoutubeServiceProvider::class
```

## Aliases do package

Em seu arquivo `config/app.php` adicione no array `aliases` a seguinte instrução:

```php
Youtube' => Laravel\Youtube\Facades\Youtube::class
```
    
## Criação do configurador

Agora você irá executar o comando:

```php
php artisan vendor:publish --provider="Laravel\Youtube\YoutubeServiceProvider"
```

Se tudo ocorreu bem, a seguinte mensagem sera exibida:

```php
Copied File [/vendor/michael/laravel-youtube/config/youtube.php] To [/config/youtube.php]
```

## Criação da tabela de tokens do YouTube

**Atenção:** Essa tabela é essencial para o funcionamento da biblioteca pois
com ela a `laravel-youtube` será capaz de armazenar os tokens retornados
do Google.

Caso você prefira criar a tabela de `tokens` sem utilizar as migrações do 
Laravel, segue o `SQL`:


```sql
CREATE TABLE `direct`.`youtubeTokens` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `access_token` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`));
```