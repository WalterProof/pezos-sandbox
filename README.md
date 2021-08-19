# Pezos Sandbox

![build](https://github.com/bzzhh/pezos-sandbox/workflows/main/badge.svg)

Tezos PHP Sandbox

Just run `make` to add the hostname to you host.

Run `make debug` to launch containers.

Run `yarn run dev-server` for the frontend.

Import data

```
\copy doctrine_migration_versions(version, executed_at, execution_time) from '~/code/pezos-sandbox/data/doctrine_migration_versions.csv' delimiter ',' CSV HEADER;
\copy exchanges(exchange_id, name, homepage) from '~/code/pezos-sandbox/data/exchanges.csv' delimiter ',' CSV HEADER;
\copy members(pub_key, registered_at, address) from '~/code/pezos-sandbox/data/members.csv' delimiter ',' CSV HEADER;
\copy tokens(token_id, contract, id, metadata, active, position) from '~/code/pezos-sandbox/data/tokens.csv' delimiter ',' CSV HEADER;
\copy token_exchanges(token_id, exchange_id, contract) from '~/code/pezos-sandbox/data/token_exchanges.csv' delimiter ',' CSV HEADER;
\copy tags(tag_id, label) from '~/code/pezos-sandbox/data/tags.csv' delimiter ',' CSV HEADER;
```

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
-   [x] have web server stats
-   [ ] explanation of signature login
-   [ ] links to other ecosystem tools (comet, teztools, tzflow..)
-   [ ] scam detection
-   [ ] prices delta percentages
-   [ ] add circulating supply editable field
-   [ ] social media activity
