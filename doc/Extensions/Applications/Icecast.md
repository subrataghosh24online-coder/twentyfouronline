## Icecast

Shell script that reports load average/memory/open-files stats of Icecast
### SNMP Extend

1. Copy the shell script, icecast-stats.sh, to the desired host (the host must be added to twentyfouronline devices)

    ```bash
    wget https://github.com/twentyfouronline/twentyfouronline-agent/raw/master/snmp/icecast-stats.sh -O /etc/snmp/icecast-stats.sh
    ```

2. Make the script executable

    ```bash
    chmod +x /etc/snmp/icecast-stats.sh
    ```

3. Verify it is working by running `/etc/snmp/icecast-stats.sh`

4. Edit your snmpd.conf file (usually `/etc/snmp/icecast-stats.sh`) and add:

    ```bash
    extend icecast /etc/snmp/icecast-stats.sh
    ```




