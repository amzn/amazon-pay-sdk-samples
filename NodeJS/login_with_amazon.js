var querystring = require('querystring')
var http = require('https')

function Login(client_id, sandbox) {
  this.client_id = client_id
  this.sandbox = sandbox
}

Login.prototype = {
	constructor: Login
	, getLoginProfile: function(accessToken, callback) {
		var decodedAccessToken = decodeURIComponent(accessToken)
		var encodeAccessToken = encodeURIComponent(decodedAccessToken)

		if (this.sandbox == true) {
		  var sandbox_str = "api.sandbox"
		} else {
		  var sandbox_str = "api"
		}

		var client_id = this.client_id

		http.get("https://" + sandbox_str + ".amazon.com/auth/o2/tokeninfo?access_token=" + encodeAccessToken, function(res) {
			res.on('data', function (chunk) {
				if (JSON.parse(chunk)['aud'] != client_id) {
					throw new Error('Invalid Access Token')
				}

				var get_options = {
				  host: sandbox_str+".amazon.com",
				  path: "/user/profile",
				  method: 'GET',
				  headers: {
				    'Authorization': "bearer " + decodedAccessToken
				  }
				}

				var get_req = http.request(get_options, function(res) {
					res.setEncoding('utf8')
				  res.on('data', function (chunk) {
				    callback(JSON.parse(chunk))
				  }).on('error', function(e) {
						callback(e.message)
					})
				})

				get_req.end()

			})
		}).on('error', function(e) {
		  callback(e.message)
		})

    process.on('uncaughtException', function (err) {
     callback(err)
    })
	}
}

module.exports = Login;
