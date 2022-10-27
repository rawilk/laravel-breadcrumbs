#!/bin/sh

composer install
vendor/bin/testbench package:discover
