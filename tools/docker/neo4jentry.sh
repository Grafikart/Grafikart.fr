#!/bin/bash
echo "Restoring backup"
neo4j-admin restore --from=/backup --force
echo "Starting neo4j"
neo4j start
tail -f /logs/neo4j.log
