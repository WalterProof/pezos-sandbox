# Pezos Sandbox

![build](https://github.com/bzzhh/pezos-sandbox/workflows/main/badge.svg)

Tezos PHP Sandbox

Just run `make` to add the hostname to you host.

Run `make debug` to launch containers.

Run `yarn run dev-server` for the frontend.

Run `docker-compose exec php ./bin/console import` to import data.

You should see something at http://pezos-sandbox.localdev/

The project architecture will try to follow domain driven design, it is directly inspired from https://leanpub.com/web-application-architecture (great book).

https://github.com/dunglas/symfony-docker is used as a base infra.

The price dynamics have been in(a)spired from https://github.com/ztepler/quipuswap-tezos-analysis-colab (thank you!).

Roadmap:

-   [x] add pool dynamics
-   [ ] add token story/economics/infos
-   [ ] add admin logs
-   [ ] use https://github.com/dipdup-net
-   [ ] improve UI (mobile version)
-   [ ] count which token is most viewed
-   [ ] tokens ordering (by liquidity pool..) 
-   [ ] use rollbar for error logs
-   [ ] have web server stats
-   [ ] explanation of signature login
-   [ ] links to other ecosystem tools (comet, teztools, tzflow..)
