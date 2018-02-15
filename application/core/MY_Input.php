<?php

class MY_Input extends CI_Input {

    function __construct()
    {
        parent::__construct();
    }

    function post($index = '', $xss_clean = TRUE)
    {
        return parent::post($index, $xss_clean);
    }

    /**
     * (Fix que corrige bug de caida al ejecutar comandos CodeIgniter por CLI)
     * https://github.com/EllisLab/CodeIgniter/issues/1890
     *
     * Fetch the IP Address
     *
     * Determines and validates the visitor's IP address.
     *
     * @return	string	IP address
     */
    public function ip_address()
    {
        if ($this->ip_address !== FALSE)
        {
            return $this->ip_address;
        }

        $proxy_ips = config_item('proxy_ips');
        if ( ! empty($proxy_ips) && ! is_array($proxy_ips))
        {
            $proxy_ips = explode(',', str_replace(' ', '', $proxy_ips));
        }

        $this->ip_address = $this->server('REMOTE_ADDR');

        if ($proxy_ips)
        {
            foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP') as $header)
            {
                if (($spoof = $this->server($header)) !== NULL)
                {
                    // Some proxies typically list the whole chain of IP
                    // addresses through which the client has reached us.
                    // e.g. client_ip, proxy_ip1, proxy_ip2, etc.
                    sscanf($spoof, '%[^,]', $spoof);

                    if ( ! $this->valid_ip($spoof))
                    {
                        $spoof = NULL;
                    }
                    else
                    {
                        break;
                    }
                }
            }

            if ($spoof)
            {
                for ($i = 0, $c = count($proxy_ips); $i < $c; $i++)
                {
                    // Check if we have an IP address or a subnet
                    if (strpos($proxy_ips[$i], '/') === FALSE)
                    {
                        // An IP address (and not a subnet) is specified.
                        // We can compare right away.
                        if ($proxy_ips[$i] === $this->ip_address)
                        {
                            $this->ip_address = $spoof;
                            break;
                        }

                        continue;
                    }

                    // We have a subnet ... now the heavy lifting begins
                    isset($separator) OR $separator = $this->valid_ip($this->ip_address, 'ipv6') ? ':' : '.';

                    // If the proxy entry doesn't match the IP protocol - skip it
                    if (strpos($proxy_ips[$i], $separator) === FALSE)
                    {
                        continue;
                    }

                    // Convert the REMOTE_ADDR IP address to binary, if needed
                    if ( ! isset($ip, $sprintf))
                    {
                        if ($separator === ':')
                        {
                            // Make sure we're have the "full" IPv6 format
                            $ip = explode(':',
                                str_replace('::',
                                    str_repeat(':', 9 - substr_count($this->ip_address, ':')),
                                    $this->ip_address
                                )
                            );

                            for ($i = 0; $i < 8; $i++)
                            {
                                $ip[$i] = intval($ip[$i], 16);
                            }

                            $sprintf = '%016b%016b%016b%016b%016b%016b%016b%016b';
                        }
                        else
                        {
                            $ip = explode('.', $this->ip_address);
                            $sprintf = '%08b%08b%08b%08b';
                        }

                        $ip = vsprintf($sprintf, $ip);
                    }

                    // Split the netmask length off the network address
                    sscanf($proxy_ips[$i], '%[^/]/%d', $netaddr, $masklen);

                    // Again, an IPv6 address is most likely in a compressed form
                    if ($separator === ':')
                    {
                        $netaddr = explode(':', str_replace('::', str_repeat(':', 9 - substr_count($netaddr, ':')), $netaddr));
                        for ($i = 0; $i < 8; $i++)
                        {
                            $netaddr[$i] = intval($netaddr[$i], 16);
                        }
                    }
                    else
                    {
                        $netaddr = explode('.', $netaddr);
                    }

                    // Convert to binary and finally compare
                    if (strncmp($ip, vsprintf($sprintf, $netaddr), $masklen) === 0)
                    {
                        $this->ip_address = $spoof;
                        break;
                    }
                }
            }
        }

        if ( ! $this->valid_ip($this->ip_address))
        {
            return $this->ip_address = '0.0.0.0';
        }

        return $this->ip_address;
    }
}
