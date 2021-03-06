'use strict'

module.exports = function(app) {

    app.service('Server', ['$http', '$q', function($http, $q) {

        this.processTrack = function(data) {
            return $http.post('/track/process', data);
        }

        this.rebuildPlaylist = function(data) {
            return $http.post('/playlist', { playlist: data });
        }

    }]);

}
