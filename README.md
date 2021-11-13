# Pezos Sandbox

![build](https://github.com/bzzhh/pezos-sandbox/workflows/main/badge.svg)

## Dev

Launch infra:

```sh
docker-compose up -d
```

Migrate db:

```sh
./bin/console/ doctrine:migrations:migrate
```

Launch PHP server:

```sh
symfony serve
```

Launch webpack:

```sh
yarn dev-server
```

Bootstrap prices:

```sh
./bin/console app:prices:bootstrap
```

## Prod

Build container:

```sh
docker build -t bzzhh/pezos:latest .
```

Run container:

```sh
docker run -p 7800:8080 bzzhh/pezos:latest
```

TODO:

- [ ] use [mercure](https://mercure.rocks)
- [ ] node polling for new blocks ([monitoring-for-new-blocks](https://github.com/blockwatch-cc/tzgo#monitoring-for-new-blocks))
- [ ] datasource selector (teztools and tzkt)
- [ ] candle charts with [chartjs-chart-financial](https://github.com/chartjs/chartjs-chart-financial)
- [ ] env vars on run instead of build, to allow using public docker image
- [ ] admin for metadata (and sync with https://github.com/jmagly/build.teztools.io/tree/main/communitydata)
