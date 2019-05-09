## Laravel YouTube - 0.0.1

![Laravel YouTube](logo.png)

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
php artisan vendor:publish
```

Se tudo ocorreu bem, a seguinte mensagem sera exibida:

```php
Copied File [/vendor/michael/laravel-youtube/config/youtube.php] To [/config/youtube.php]
```