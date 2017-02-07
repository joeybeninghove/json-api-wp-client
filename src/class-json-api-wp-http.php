<?php

class Json_Api_Wp_Http {
    public function get( $resource ) {
        $options = $this->options( "GET", $resource );
        if ( $resource->has_id() ) {
            return $this->request(
                "GET", Json_Api_Wp_Url::get_one( $resource ), $options
            );
        } else {
            return $this->request(
                "GET", Json_Api_Wp_Url::get_all( $resource ), $options
            );
        }
    }

    public function post( $resource ) {
        $options = $this->options( "POST", $resource );
        return $this->request(
            "POST", Json_Api_Wp_Url::create( $resource ),$options
        );
    }

    public function request( $method, $url, $options=[] ) {
        if ( $method == "GET" ) {
            return wp_remote_get( $url, $options );
        } else {
            return wp_remote_post( $url, $options );
        }
    }

    public function credentials( $resource ) {
        $class = get_class( $resource );

        return base64_encode(
            $class::$username . ":" . $class::$password
        );
    }

    public function options( $method, $resource ) {
        if ( in_array( $method, ["PATCH", "DELETE"] ) ) {
            throw new Json_Api_Wp_Exception(
                "PATCH and DELETE are not supported"
            );
        }

        $options = [
            "method" => $method
        ];

        $options["headers"] = [
            "Accept" => "application/vnd.api+json",
            "Authorization" => "Basic " . $this->credentials( $resource )
        ];

        if ( in_array( $method, ["POST", "PUT"] ) ) {
            $options["headers"]["Content-Type"] = "application/vnd.api+json";
            $options["body"] = $resource->to_json();
        }

        return $options;
    }
}
