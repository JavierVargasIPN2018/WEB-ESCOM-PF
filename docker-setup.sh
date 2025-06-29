#!/bin/bash

# Habilita el módulo de reescritura de Apache
a2enmod rewrite

# Inicia Apache en primer plano
apache2-foreground
