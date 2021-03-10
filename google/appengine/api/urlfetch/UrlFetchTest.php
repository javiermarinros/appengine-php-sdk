<?php
/**
 * Copyright 2021 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 */

namespace google\appengine\api\urlfetch;


require_once __DIR__ . '/UrlFetch.php';

use google\appengine\api\urlfetch\UrlFetch;
use google\appengine\runtime\ApplicationError;
use google\appengine\testing\ApiProxyTestBase;
use google\appengine\URLFetchServiceError;
use google\appengine\URLFetchRequest;
use google\appengine\URLFetchRequest\RequestMethod;
use google\appengine\URLFetchResponse;


class UrlFetchTest extends ApiProxyTestBase {

  public function setUp() {
    parent::setUp();
    $this->_SERVER = $_SERVER;
  }

  public function tearDown() {
    $_SERVER = $this->_SERVER;
    parent::tearDown();
  }

  public function testGetFetchWithDefaultArgs() {
    $urlfetch = new UrlFetch();
    $url = "http://www.google.com"; 
    $req = new URLFetchRequest();
    $resp = new URLFetchResponse();
    $req->setUrl($url);
    $req->setMethod(RequestMethod::GET);
    $this->apiProxyMock->expectCall('urlfetch', 'Fetch', $req, $resp);
    $result = $urlfetch->fetch($url, 'GET');
    $this->assertEquals($resp, $result);
  }

  public function testGetFetchWithHeader() {
    $urlfetch = new UrlFetch();
    $url = "http://www.google.com"; 
    $req = new URLFetchRequest();
    $resp = new URLFetchResponse();
    $req->setUrl($url);
    $req->setMethod(RequestMethod::GET); 
    $header = new URLFetchRequest\Header();
    $header->setKey('header1');
    $header->setValue('value1');
    $req->addHeader($header);
    $header_arr = ['header1' => 'value1'];
    $this->apiProxyMock->expectCall('urlfetch', 'Fetch', $req, $resp);
    $result = $urlfetch->fetch($url, 'GET', $header_arr);
    $this->assertEquals($resp, $result);
  }

  public function testGetFetchWithBasicPayload() {
    $urlfetch = new UrlFetch();
    $url = "http://www.google.com"; 
    $req = new URLFetchRequest();
    $resp = new URLFetchResponse();
    $req->setUrl($url);
    $req->setMethod(RequestMethod::PUT); 
    $payload = "Example Payload";
    $req->setPayload($payload);
    $this->apiProxyMock->expectCall('urlfetch', 'Fetch', $req, $resp);
    $result = $urlfetch->fetch($url, 'PUT', null,  $payload);
    $this->assertEquals($resp, $result);
  }

  public function testGetFetchWithUrlEncodedPayload() {
    $urlfetch = new UrlFetch();
    $url = "http://www.google.com"; 
    $req = new URLFetchRequest();
    $resp = new URLFetchResponse();
    $req->setUrl($url);
    $req->setMethod(RequestMethod::PUT); 
    $payload = http_build_query(
        array(
            'var1' => 'some content',
            'var2' => 'some content2'
        )
    );
    $req->setPayload($payload);
    $this->apiProxyMock->expectCall('urlfetch', 'Fetch', $req, $resp);
    $result = $urlfetch->fetch($url, 'PUT', null,  $payload);
    $this->assertEquals($resp, $result);
  }


  public function testMiscConfiguration() {
    $follow_redirects = True;
    $allow_truncated = False;
    $deadline = 5.0;
    $urlfetch = new UrlFetch();
    $url = "http://www.google.com"; 
    $req = new URLFetchRequest();
    $resp = new URLFetchResponse();
    $req->setUrl($url);
    $req->setMethod(RequestMethod::GET);
    $req->setFollowredirects($follow_redirects);
    $req->setDeadline($deadline);
    $this->apiProxyMock->expectCall('urlfetch', 'Fetch', $req, $resp);
    $result = $urlfetch->fetch(
        $url, 
        'GET',
        null,
        null,
        $allow_truncated,
        $follow_redirects, 
        $deadline);
    $this->assertEquals($resp, $result);
    $this->assertEquals($resp->getContentwastruncated(), $allow_truncated);
  }
}