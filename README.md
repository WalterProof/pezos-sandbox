# Pezos Sandbox

Tezos PHP Sandbox

Just run `make` to add the hostname to you host.

Run `make debug` to launch containers.

Run `yarn run dev-server` for the frontend.

You should see something at http://pezos-sandbox.localdev/

Generate a test password `docker-compose exec php ./bin/console security:encode-password secret 'PezosSandbox\Application\Members\Member'`
