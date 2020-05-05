/**
 * Async api calls and writes response to console
 */
const https = require('https');

const request = id =>
  new Promise((resolve, reject) => {
    const req = https.request(
      {
        auth: 'interview-test@cartoncloud.com.au:test123456',
        hostname: 'api.cartoncloud.com.au',
        port: 443,
        path: `/CartonCloud_Demo/PurchaseOrders/${id}?version=5&associated=true`,
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        }
      },
      (res) => {
        let body = '';
        res.on('data', d => {
          // append all data into a single string
          body += d;
        });

        res.on('end', () => {
          // decode json response and extract only 'data' part
          const data = JSON.parse(body);
          resolve(data.data);
        });
      },
    );

    req.on('error', error => {
      reject(error);
    });

    req.end();
  });

// Take the 3rd arg ('[1234,2345,3456]') from "node request.js '[1234,2345,3456]'"
// Decode it into an id array
const ids = JSON.parse(process.argv[2]);
// Async calls and wait for them all done
Promise.all(ids.map(id => request(id)))
  // data are now all responses from async calls
  // In php, this is pretty hard to do, need Guzzle lib
  // But in nodejs, this is native.
  .then(data => console.log(JSON.stringify(data)));
