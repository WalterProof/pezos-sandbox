# Pezos Sandbox

![build](https://github.com/bzzhh/pezos-sandbox/workflows/reboot/badge.svg)

## Dev

Launch infra:

```
docker-compose up -d
```

Launch PHP server:

```
symfony serve
```

Launch webpack:

```
yarn dev-server
```

## Prod

Build container:

```
docker build -t bzzhh/pezos:latest .
```

Run container:

```
docker run -p 7800:8080 bzzhh/pezos:latest
```

TODO:

-   [ ] use https://mercure.rocks
-   [ ] node polling for new blocks like https://github.com/blockwatch-cc/tzgo#monitoring-for-new-blocks
-   [ ] datasource selector (teztools and tzkt)
-   [ ] candle charts with https://github.com/chartjs/chartjs-chart-financial
-   [ ] env vars on run instead of build, to allow using public docker image
