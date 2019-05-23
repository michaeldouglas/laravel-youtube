## Laravel YouTube - 0.0.21

[![Latest Stable Version](https://poser.pugx.org/michael/laravel-youtube/v/stable)](https://packagist.org/packages/michael/laravel-youtube)
[![Total Downloads](https://poser.pugx.org/michael/laravel-youtube/downloads)](https://packagist.org/packages/michael/laravel-youtube)
[![License](https://poser.pugx.org/michael/laravel-youtube/license)](https://packagist.org/packages/michael/laravel-youtube)
[![Maintainability](https://api.codeclimate.com/v1/badges/bd5c0e5e25ae75c7189c/maintainability)](https://codeclimate.com/github/michaeldouglas/laravel-youtube/maintainability)
[![Build Status](https://travis-ci.org/michaeldouglas/laravel-youtube.svg?branch=master)](https://travis-ci.org/michaeldouglas/laravel-youtube)

![Laravel YouTube](logo.png)

**Atention:** `Documentation in english please` [click for here](README-EN.md)

O **laravel-youtube** consome a API do **YouTube** e prove uma forma simples de 
gerar e manipular seus videos no YouTube. 

Também é capaz de devolver estatísticas, relatórios relacionados aos seus 
vídeos no YouTube e criar o **RTMP** e **chave de transmissão** para uma transmissão
 ao vivo.

## Compatibilidade

 PHP >= 7.1
 Laravel 5.x
 
 
 ## Instalação
 
Abra o arquivo `composer.json` e insira a seguinte instrução:
 
     "require": {
         "michael/laravel-youtube": "0.0.21"
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
Youtube => Laravel\Youtube\Facades\Youtube::class
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

Ou você pode executar a migração que o projeto irá instalar e para isso basta executar:

```bash
php artisan migrate
```

## Subir video no YouTube

**Atenção: Se o video for muito grande, provavelmente, você terá que aumentar o tempo limite do seu servidor, para que não seja mostrado erro de TimeOut!**

Para subir o video para o `YouTube` basta que você diga para a `Laravel YouTube` onde o video encontra-se e também fornecer os parâmetros:

  - `title` - Título do video
  - `description` - descrição do video
  - `tags`
  - `category_id` - Em qual categoria o seu video será colocado.
  - E por último a `privacidade` do video
    
Veja a seguir um exemplo de como subir um video para o YouTube"

```php
<?php

$path = public_path().'/video/video.mp4';

$video = YouTube::uploadVideo($path, [
    'title'       => 'Laravel YouTube',
    'description' => 'Laravel YouTube',
    'tags'	  => ['laravel', 'laravel-youtube', 'php', 'package'],
    'category_id' => 10
], 'public');

return ["idVideo" => $video->getIdVideo(), "details" => $video->getSnippet()];
```

## Verificar se o video existe

Para verificar se um video existe, basta, fornecer o id dele para 
o método `checkExistVideo()`, da seguinte maneira:

```php
<?php 

$id = "O ID DO VIDEO";
$existVideo = YouTube::checkExistVideo($id);

return ['status' => $existVideo];
```

## Obter detalhes do video

Para obter detalhes de um video, basta, fornecer o id dele para 
o método `checkExistVideo()`, da seguinte maneira:

```php
<?php 

$id = "O ID DO VIDEO";
$existVideo = YouTube::getDetailsVideo($id);

return ["details" => $detailsVideo];
```

## Excluir video

A operação para excluir um video é bem simples basta fornecer o identificador do video e chamar o método `excluir()`.

Veja a seguir um exemplo:

```php
<?php 

$id = "O ID DO VIDEO";
$video = YouTube::delete($id);

return ["excluir" => $video];
```

## Criação de eventos Ao Vivo

Para criar um evento Ao Vivo, basta chamado o método `createEventRTMP()`
e fornecer os parâmetros:

  - `Data de inicio`
  - `Data de Termino`
  - `Titulo do video`
  - **opcional:** `Privacidade` - O default da privacidade é: unlisted
  - **opcional:** `Linguagem` - O default da linguagem é: Portuguese (Brazil)
  - **opcional:** `Tags` - O default da tags é: michael,laravel-youtube
  
Exemplo de uso:

```php
<?php 

YouTube::createEventRTMP("2019-05-13 22:00:00", "2019-05-13 23:00:00", "Evento teste");
```

### Valores opcionais

Exemplo de uso com os **valores opcionais**:

```php
<?php

YouTube::createEventRTMP("2019-05-13 22:00:00", "2019-05-13 23:00:00", "Evento teste", "unlisted", "Portuguese (Brazil)", "michael,laravel-youtube");
```

Caso a criação seja feita com sucesso você terá como retorno um 
`Json` com todos os valores do evento.

## Lista de eventos Ao Vivo

Para listar os eventos Ao Vivo, basta chamar o método `listEventsBroadcasts()`, 
da seguinte maneira:

```php
<?php

$video = YouTube::listEventsBroadcasts();

return ["list" => $video];
```

Caso exista uma lista de videos, então, será retornando um `Json` com todos os eventos.