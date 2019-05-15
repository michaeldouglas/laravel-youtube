## Laravel YouTube - 0.0.15

[![Latest Stable Version](https://poser.pugx.org/michael/laravel-youtube/v/stable)](https://packagist.org/packages/michael/laravel-youtube)
[![Total Downloads](https://poser.pugx.org/michael/laravel-youtube/downloads)](https://packagist.org/packages/michael/laravel-youtube)
[![License](https://poser.pugx.org/michael/laravel-youtube/license)](https://packagist.org/packages/michael/laravel-youtube)
[![Maintainability](https://api.codeclimate.com/v1/badges/bd5c0e5e25ae75c7189c/maintainability)](https://codeclimate.com/github/michaeldouglas/laravel-youtube/maintainability)

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
         "michael/laravel-youtube": "0.0.15"
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

Ou você pode executar a migração que o projeto irá instalar e para isso basta executar:

```bash
php artisan migrate
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

## Criação de eventos Ao Vivo

Para criar um evento Ao Vivo, basta chamado o método `createEventRTMP()`
e fornecer os parâmetros:

  - Data de inicio
  - Data de Termino
  - Descrição do video
  
Exemplo de uso:

```php
<?php 

YouTube::createEventRTMP("2019-05-13 22:00:00", "2019-05-13 23:00:00", "Evento teste");
```

Caso a criação seja feita com sucesso você terá como retorno o seguinte 
**Json**:

```json
{
   "broadcast_response":{
      "etag":"\"XpPGQXPnxQJhLgs6enD_n8JR4Qk\/KArWvJpXFjZrv9du84ZDqV9Zv8Y\"",
      "id":"aBI7Hmbr3TY",
      "kind":"youtube#liveBroadcast",
      "snippet":{
         "actualEndTime":null,
         "actualStartTime":null,
         "channelId":"UCshtOFXi4VNuqUxYNolH54A",
         "description":"",
         "isDefaultBroadcast":false,
         "liveChatId":"Cg0KC2FCSTdIbWJyM1RZ",
         "publishedAt":"2019-05-15T15:11:07.000Z",
         "scheduledEndTime":"2019-05-15T16:00:00.000Z",
         "scheduledStartTime":"2019-05-15T15:11:06.000Z",
         "title":"NOVOMICHAEL",
         "thumbnails":{
            "default":{
               "height":90,
               "url":"https:\/\/i.ytimg.com\/vi\/aBI7Hmbr3TY\/default_live.jpg",
               "width":120
            },
            "medium":{
               "height":180,
               "url":"https:\/\/i.ytimg.com\/vi\/aBI7Hmbr3TY\/mqdefault_live.jpg",
               "width":320
            },
            "high":{
               "height":360,
               "url":"https:\/\/i.ytimg.com\/vi\/aBI7Hmbr3TY\/hqdefault_live.jpg",
               "width":480
            }
         }
      },
      "status":{
         "lifeCycleStatus":"created",
         "liveBroadcastPriority":null,
         "privacyStatus":"unlisted",
         "recordingStatus":"notRecording"
      },
      "contentDetails":{
         "boundStreamId":null,
         "boundStreamLastUpdateTimeMs":null,
         "closedCaptionsType":"closedCaptionsHttpPost",
         "enableAutoStart":true,
         "enableClosedCaptions":true,
         "enableContentEncryption":true,
         "enableDvr":true,
         "enableEmbed":true,
         "enableLowLatency":false,
         "latencyPreference":"normal",
         "mesh":null,
         "projection":"rectangular",
         "recordFromStart":true,
         "startWithSlate":true,
         "stereoLayout":null,
         "monitorStream":{
            "broadcastStreamDelayMs":0,
            "embedHtml":"<iframe width=\"425\" height=\"344\" src=\"https:\/\/www.youtube.com\/embed\/aBI7Hmbr3TY?autoplay=1&livemonitor=1\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen><\/iframe>",
            "enableMonitorStream":true
         }
      }
   },
   "video_response":{
      "etag":"\"XpPGQXPnxQJhLgs6enD_n8JR4Qk\/NsnweYWDqMlCTPjqXVv_5M2yvIE\"",
      "id":"aBI7Hmbr3TY",
      "kind":"youtube#video",
      "snippet":{
         "categoryId":"24",
         "channelId":"UCshtOFXi4VNuqUxYNolH54A",
         "channelTitle":"mdba araujo",
         "defaultAudioLanguage":"pt-BR",
         "defaultLanguage":"pt-BR",
         "description":"",
         "liveBroadcastContent":"upcoming",
         "publishedAt":"2019-05-15T15:11:07.000Z",
         "tags":[
            "video"
         ],
         "title":"NOVOMICHAEL",
         "thumbnails":{
            "default":{
               "height":90,
               "url":"https:\/\/i.ytimg.com\/vi\/aBI7Hmbr3TY\/default_live.jpg",
               "width":120
            },
            "medium":{
               "height":180,
               "url":"https:\/\/i.ytimg.com\/vi\/aBI7Hmbr3TY\/mqdefault_live.jpg",
               "width":320
            },
            "high":{
               "height":360,
               "url":"https:\/\/i.ytimg.com\/vi\/aBI7Hmbr3TY\/hqdefault_live.jpg",
               "width":480
            },
            "standard":{
               "height":480,
               "url":"https:\/\/i.ytimg.com\/vi\/aBI7Hmbr3TY\/sddefault_live.jpg",
               "width":640
            },
            "maxres":{
               "height":720,
               "url":"https:\/\/i.ytimg.com\/vi\/aBI7Hmbr3TY\/maxresdefault_live.jpg",
               "width":1280
            }
         },
         "localized":{
            "description":"",
            "title":"NOVOMICHAEL"
         }
      }
   },
   "stream_response":{
      "etag":"\"XpPGQXPnxQJhLgs6enD_n8JR4Qk\/oCb5vfEaZ77dFXpg3B30AbKICTk\"",
      "id":"shtOFXi4VNuqUxYNolH54A1557933070227741",
      "kind":"youtube#liveStream",
      "snippet":{
         "channelId":"UCshtOFXi4VNuqUxYNolH54A",
         "description":"",
         "isDefaultStream":false,
         "publishedAt":"2019-05-15T15:11:10.000Z",
         "title":"NOVOMICHAEL"
      },
      "cdn":{
         "format":"1080p",
         "frameRate":"30fps",
         "ingestionType":"rtmp",
         "resolution":"1080p",
         "ingestionInfo":{
            "backupIngestionAddress":"rtmp:\/\/b.rtmp.youtube.com\/live2?backup=1",
            "ingestionAddress":"rtmp:\/\/a.rtmp.youtube.com\/live2",
            "streamName":"tsaa-7srb-q2jt-0w5a"
         }
      }
   },
   "bind_broadcast_response":{
      "etag":"\"XpPGQXPnxQJhLgs6enD_n8JR4Qk\/aNvXlFtYkV7VOlsn2K8HzRhWSwU\"",
      "id":"aBI7Hmbr3TY",
      "kind":"youtube#liveBroadcast",
      "contentDetails":{
         "boundStreamId":"shtOFXi4VNuqUxYNolH54A1557933070227741",
         "boundStreamLastUpdateTimeMs":"2019-05-15T15:11:10.243Z",
         "closedCaptionsType":"closedCaptionsHttpPost",
         "enableAutoStart":true,
         "enableClosedCaptions":true,
         "enableContentEncryption":true,
         "enableDvr":true,
         "enableEmbed":true,
         "enableLowLatency":false,
         "latencyPreference":"normal",
         "mesh":null,
         "projection":"rectangular",
         "recordFromStart":true,
         "startWithSlate":true,
         "stereoLayout":null,
         "monitorStream":{
            "broadcastStreamDelayMs":0,
            "embedHtml":"<iframe width=\"425\" height=\"344\" src=\"https:\/\/www.youtube.com\/embed\/aBI7Hmbr3TY?autoplay=1&livemonitor=1\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen><\/iframe>",
            "enableMonitorStream":true
         }
      }
   }
}
``` 