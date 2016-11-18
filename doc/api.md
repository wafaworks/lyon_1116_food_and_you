# Api iOS access tokens
HTTP_HOST        = http://foodandyou.soluti.fr/
CLIENT_ID        = 1
CLIENT_RANDOM_ID = 5xwbnpsjv0kk8wok8g0sg4k4kowkck8sw0cc8go4c4socc4wwk
CLIENT_SECRET    = 66ig8g2ozbk8ko0okws8ko44gks4sswk80s0oogocoooskko40

# Available API endpoints
# 1. Authorization
## 1.1 Get access token, via user and password
###### Request
 **URL:**      [HTTP_HOST]oauth/v2/token
 **Method:**   POST
 
| Parameter           | Syntax                           | Required          | Example                                               |
| ------------------- | -------------------------------- | ----------------- | ----------------------------------------------------- |
| client_id           | [CLIENT_ID]_[CLIENT_RANDOM_ID]   | yes               | 1_5xwbnpsjv0kk8wok8g0sg4k4kowkck8sw0cc8go4c4socc4wwk  |
| client_secret       | [CLIENT_SECRET]                  | yes               | 66ig8g2ozbk8ko0okws8ko44gks4sswk80s0oogocoooskko40    |
| grant_type          | password                         | yes               | password                                              |
| username            | {username of the member}         | yes               | soluti@soluti.fr                                      |
| password            | {password of the member}         | yes               | recette                                               |
 
###### RESPONSE:

    {
		"access_token": "MDFkYmNhNTQyYzk5ZDljYzhmZTU3ODNiODZjZjkyYjJjY2RkYjcyNGEzMzc1MDFkZGZlOWFmZjZiM2YyZDYzZQ",
		"expires_in": 3600,
		"token_type": "bearer",
		"scope": null,
		"refresh_token": "OGYzMWZlNjg1Mzc3NTVjYjMwMGI1ZGU4N2MyMDJjMjViNjVjOTUzMzlkMWNhOWExNmI5MWM5Mzg4ZDk5NmU2Nw"
	}

## 1.2 Get access token, via facebook
###### Request
 **URL:**      [HTTP_HOST]/oauth/v2/token
 **Method:**   POST

| Parameter           | Syntax                           | Required          | Example                                               |
| ------------------- | -------------------------------- | ----------------- | ----------------------------------------------------- |
| client_id           | [CLIENT_ID]_[CLIENT_RANDOM_ID]   | yes               | 1_5xwbnpsjv0kk8wok8g0sg4k4kowkck8sw0cc8go4c4socc4wwk  |
| client_secret       | [CLIENT_SECRET]                  | yes               | 66ig8g2ozbk8ko0okws8ko44gks4sswk80s0oogocoooskko40    |
| grant_type          | http://fy.com/facebook           | yes               | http://fy.com/facebook                                |
| id                  | {facebook user id}               | yes               | 10153942518988028                                     |
| password            | {facebook access token}          | yes               | CAAHgMdtN3nEBAFWYijIdPhVJCVV...                       |

###### RESPONSE:

    {
		"access_token": "NWIwMGU3OWFmNTAwOWJkNjAzN2JkNTRlNzE5ZTg3MTRmMzVjMzA0OTM4YWRjN2I4YzJjNTk4ZGFmNmQwNDYwMQ",
		"expires_in": 3600,
		"token_type": "bearer",
		"scope": null,
		"refresh_token": "MDNlZjQ2NTA0OWZjMTM2N2NiZTU0ZmM1ZDA2YWMyZWVmMjM1ZDRkMjA3NmIzZmY5MmJjYzZiNmQ4Yjg5MmQ2ZQ"
	}

## 1.3 Refresh access token
###### Request
 **URL:**      [HTTP_HOST]/oauth/v2/token
 **Method:**   POST

| Parameter           | Syntax                           | Required          | Example                                                                                |
| ------------------- | -------------------------------- | ----------------- | -------------------------------------------------------------------------------------- |
| client_id           | [CLIENT_ID]_[CLIENT_RANDOM_ID]   | yes               | 1_5xwbnpsjv0kk8wok8g0sg4k4kowkck8sw0cc8go4c4socc4wwk                                   |
| client_secret       | [CLIENT_SECRET]                  | yes               | 66ig8g2ozbk8ko0okws8ko44gks4sswk80s0oogocoooskko40                                     |
| grant_type          | refresh_token                    | yes               | refresh_token                                                                          |
| refresh_token       | {previous refresh token}         | yes               | MDNlZjQ2NTA0OWZjMTM2N2NiZTU0ZmM1ZDA2YWMyZWVmMjM1ZDRkMjA3NmIzZmY5MmJjYzZiNmQ4Yjg5MmQ2ZQ |

###### RESPONSE:

    {
        "access_token": "MWFkZDgwNjhhZGVhNDliMjNhYzQwZmY0MTg3ZTRkNWZmMGZlYjM1OWEzZTBiM2QzMmQzYTU3MjU0OTQ5ZjNlNQ",
        "expires_in": 3600,
        "token_type": "bearer",
        "scope": null,
        "refresh_token": "ZGNlY2E3MzcwYTFjMTM1MzQ4OGNhZTVjNWYyMTdkMTcxN2UwY2EzNWY0ZGM3MWJkN2IzMzU4OGUyMjk1NzU4Nw"
    }

## 2 Get next events
###### Request
 **URL:**      [HTTP_HOST]/api/v1/event/list
 **Method:**   GET

 **HEADERS:**
 
| Parameter           | Syntax                           | Required          | Example                                                                                       |
| ------------------- | -------------------------------- | ----------------- | --------------------------------------------------------------------------------------------- |
| Authorization       | Bearer [ACCESS_TOKEN]            | yes               | Bearer MDFkYmNhNTQyYzk5ZDljYzhmZTU3ODNiODZjZjkyYjJjY2RkYjcyNGEzMzc1MDFkZGZlOWFmZjZiM2YyZDYzZQ |


###### RESPONSE:
    {
        [
            {
                "event_id": 2780,
                "restaurant_name": "Le Guillaume",
                "restaurant_street": "rue Raymond",
                "start_date": "5456464552",
                "restaurant_picture": "/uploads/media/restaurant/0001/03/e96ca9b31579e787a871a28ff82e6ebfafd8ea7c.jpeg"
            }
            ...
        ]
    }

## 3 Get event info
###### Request
 **URL:**      [HTTP_HOST]/api/v1/event/get/{event_id}
 **Method:**   GET

 **HEADERS:**
 
| Parameter           | Syntax                           | Required          | Example                                                                                       |
| ------------------- | -------------------------------- | ----------------- | --------------------------------------------------------------------------------------------- |
| Authorization       | Bearer [ACCESS_TOKEN]            | yes               | Bearer MDFkYmNhNTQyYzk5ZDljYzhmZTU3ODNiODZjZjkyYjJjY2RkYjcyNGEzMzc1MDFkZGZlOWFmZjZiM2YyZDYzZQ |


###### RESPONSE:

**200**

    {
        [
            {
                "applicant_recipe_id": 2573,
                "dish_name": "Est voluptas suscipit ea quis aspernatur soluta.",
                "dish_type": "0entry",
                "dish_image": "/uploads/media/recipe/0001/01/thumb_500_recipe_big.jpeg"
            },
            {
                "applicant_recipe_id": 2574,
                "dish_name": "Est voluptas suscipit ea quis aspernatur soluta.",
                "dish_type": "1main",
                "dish_image": "/uploads/media/recipe/0001/01/thumb_501_recipe_big.jpeg"
            },
            {
                "applicant_recipe_id": 2575,
                "dish_name": "Est voluptas suscipit ea quis aspernatur soluta.",
                "dish_type": "2dessert",
                "dish_image": "/uploads/media/recipe/0001/01/thumb_502_recipe_big.jpeg"
            }
        ]
    }

**404** Event not found


## 4 Get rating info
###### Request
 **URL:**      [HTTP_HOST]
 **Method:**   GET

 **HEADERS:**
 
| Parameter           | Syntax                           | Required          | Example                                                                                       |
| ------------------- | -------------------------------- | ----------------- | --------------------------------------------------------------------------------------------- |
| Authorization       | Bearer [ACCESS_TOKEN]            | yes               | Bearer MDFkYmNhNTQyYzk5ZDljYzhmZTU3ODNiODZjZjkyYjJjY2RkYjcyNGEzMzc1MDFkZGZlOWFmZjZiM2YyZDYzZQ |


###### RESPONSE:

**200**

       {
         "member": {
           "member_id": 72,
           "first_name": "XXXX",
           "last_name": "XXXXX"
         },
         "member_event_ratings": {
           "restaurant": 3,
           "event": 2
         },
         "dishes": {
           "33": {
             "applicant_recipe_id": 33,
             "rating": {
               "visual": 4,
               "taste": 3
             },
             "image": "/uploads/media/restaurant/0001/01/2501456e30c2ddf3630c1a1eb2b889e9db2d0ec6.jpeg"
           },
           "35": {
             "applicant_recipe_id": 35,
             "rating": {
               "visual": 3,
               "taste": 4
             }
           },
           "37": {
             "applicant_recipe_id": 37,
             "rating": {
               "visual": 5,
               "taste": 5
             }
           }
         }
       }

**If rating is not set, -1 will be returned**

**401** User not participating at event
**404** Event not found
**404** Member by email not found??


## 5 Submit rating info
###### Request
 **URL:**      [HTTP_HOST]/api/v1/event/ratings/{event_id}?friendEmail={email}
 **Method:**   POST

 **HEADERS:**
 
| Parameter           | Syntax                           | Required          | Example                                                                                       |
| ------------------- | -------------------------------- | ----------------- | --------------------------------------------------------------------------------------------- |
| Authorization       | Bearer [ACCESS_TOKEN]            | yes               | Bearer MDFkYmNhNTQyYzk5ZDljYzhmZTU3ODNiODZjZjkyYjJjY2RkYjcyNGEzMzc1MDFkZGZlOWFmZjZiM2YyZDYzZQ |

**POST JSON**

| Parameter           | Syntax                           | Required                         | Example                                                       |
| ------------------- | -------------------------------- | -------------------------------- | ------------------------------------------------------------- |
| type                | recipe / restaurant / event      | yes                              | recipe                                                          |
| applicant_recipe_id | {applicant recipe ID}            | no (required only for dish type) | 66ig8g2ozbk8ko0okws8ko44gks4sswk80s0oogocoooskko40            |
| rating              | rating or object*                | yes                              | {"visual": 5,"taste":4}                                       |

\* see examples

**For dish type**

    {
        "type": "recipe",
        "applicant_recipe_id": 2574,
        "rating": {
            "visual": 4,
            "taste": 3
        }
    }
    
**For restaurant type**

    {
        "type": "restaurant",
        "rating": 5
    }
    
**For event type**

    {
        "type": "event",
        "rating": 4
    }

###### RESPONSE:

**204** Rating is saved
**401** User not participating at event
**404** Event not found
**404** Member by email not found??


## 6 Submit image
###### Request
 **URL:**      [HTTP_HOST]/api/v1/event/image/{event_id}?friendEmail={email}
 **Method:**   POST
 **Enctype**   x-www-form-urlencoded

 **HEADERS:**
 
| Parameter           | Syntax                           | Required          | Example                                                                                       |
| ------------------- | -------------------------------- | ----------------- | --------------------------------------------------------------------------------------------- |
| Authorization       | Bearer [ACCESS_TOKEN]            | yes               | Bearer MDFkYmNhNTQyYzk5ZDljYzhmZTU3ODNiODZjZjkyYjJjY2RkYjcyNGEzMzc1MDFkZGZlOWFmZjZiM2YyZDYzZQ |

**POST FORM**

| Parameter           | Syntax                           | Required                         | Example                                                       |
| ------------------- | -------------------------------- | -------------------------------- | ------------------------------------------------------------- |
| type                | recipe / restaurant / event        | yes                              | dish                                                          |
| applicantRecipe     | {applicant recipe ID}            | no (required only for dish type) | 66ig8g2ozbk8ko0okws8ko44gks4sswk80s0oogocoooskko40            |
| media               | file to upload                   | yes                              |                                                               |

###### RESPONSE:

**201** Created (link to image)
**401** User not participating at event
**404** Event not found
**404** Member by email not found??

## 7 Submit user device
###### Request
 **URL:**      [HTTP_HOST]/api/v1/device
 **Method:**   POST
 **Enctype**   x-www-form-urlencoded

 **HEADERS:**
 
| Parameter           | Syntax                           | Required          | Example                                                                                       |
| ------------------- | -------------------------------- | ----------------- | --------------------------------------------------------------------------------------------- |
| Authorization       | Bearer [ACCESS_TOKEN]            | yes               | Bearer MDFkYmNhNTQyYzk5ZDljYzhmZTU3ODNiODZjZjkyYjJjY2RkYjcyNGEzMzc1MDFkZGZlOWFmZjZiM2YyZDYzZQ |

**POST FORM**

| Parameter           | Syntax                    | Required           | Example        |
| ------------------- | ------------------------- | -------------------| ---------------|
| device_token[token]               | {device_token}            | yes                | token          |
| device_token[os]                  | {os ident string}         | yes                | os             |

###### RESPONSE:

**201** Created / updated
**400** Bad request

## General status codes used for all requests
 - 200 OK
 - 201 Created
 - 204 No Content
 - 400 Bad request
 - 401 Unauthorized
 - 403 Forbidden
 - 404 Not found
 - 500 Internal server error
