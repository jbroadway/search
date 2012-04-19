; <?php /*

[Search]

; To choose a back-end search engine, uncomment one of the following
; options:

backend = elasticsearch
;backend = indextank

[ElasticSearch]

; ElasticSearch server configuration goes here:

server1[host] = localhost
server1[port] = 9200

; To add additional servers, use:

;server2[host] = localhost
;server2[port] = 9201

[IndexTank]

; IndexTank server configuration goes here:

public_api_url = "http://*****.api.indextank.com/"

private_api_url = "http://:********************.api.indextank.com/"

index_name = "********"

[Admin]

handler = search/admin
name = Search

; */ ?>