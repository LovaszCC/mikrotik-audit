## Mikrotik Audit System Demo (I just wanted to test out the REST API 😃)

This is a simple system that allows you to audit your Mikrotik devices. It is built using Laravel 11 and a bit of
livewire. The system gives you access to check the configuration on your mikrotik device, without storeing any data! The
system is built with the following features:

- Running firewall checks
    - Check for any 'unprotected' ports, which means any port that does not have a src control attribute
    - Check for having the implicit deny rules
    - Check for any totally unprotected input chain rule
- Running NAT checks
    - Check for any DNAT rule
- Running VPN checks
    - Check for having PPTP VPN enabled
    - Check for having L2TP VPN enabled
    - Check for having SSTP VPN enabled
    - Check for having OpenVPN enabled
        - Check for certificates

### We are using the following packages:

- evilfreelancer/routeros-api-php for communicating with the mikrotik device which are not capable of using the
  REST API

### Installation

1. Clone the repository
2. Copy your .env.example to .env and fill in the necessary details
3. Run `composer install`
4. Run `npm install`
5. Run `npm run dev`

