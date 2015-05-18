<!DOCTYPE html>
<html ng-app="Sounderp">
<head>
    <title>Sounderp</title>
    <link href="/js/components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link href="/css/main.css" rel="stylesheet" />
</head>
<body>
    <div class="container-fluid">
        <div class="col-md-8">
            <div ng-controller="AuthController"></div>
            <div ng-controller="PlayerController" class="jumbotron" id="currently-playing">
                <img class="thumbnail" width="150" height="150" ng-src="{{ current.art.full }}" />
                <h1>{{ current.title }}</h1>
                <p>{{ current.artist }}</p>
                <div class="progress">
                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="{{ current.progress }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ current.progress }}%;">
                        <!-- 60% -->
                    </div>
                </div>
                <button ng-click="unmutePlayer()" ng-if="muted" class="btn btn-info"><span class="glyphicon glyphicon-volume-off"></span></button>
                <button ng-click="mutePlayer()" ng-if="!muted" class="btn btn-info"><span class="glyphicon glyphicon-volume-up"></span></button>
            </div>
            <div ng-controller="ListController">
                <div class="form-group">
                    <input autocomplete="off" placeholder="Search" class="form-control" ng-model="filterList" />
                </div>
                <ul class="list-group media-list" id="playlist">
                    <li class="media list-group-item" ng-show="!$last" ng-class="{disabled: item.status == 'processing'}" style="margin-top:0;" ng-repeat="item in tracks | filter: filterList">
                        <div class="media-left">
                            <i ng-if="item.status === 'processing'" class="loading-icon fa fa-circle-o-notch fa-spin"></i>
                            <img class="media-object" width="75" height="75" ng-src="{{ item.art.thumbnail }}" />
                        </div>

                        <div class="media-body">
                            <div class="btn-group btn-group-sm pull-right" role="group">
                                    <a ng-repeat="source in item.sources" href="{{ source.url }}" target="_blank" class="btn btn-info" aria-label="Permalink">
                                        <span class="fa fa-{{ source.type }}"></span>
                                    </a>
                                <a ng-click="playPreview(item)" ng-if="item.preview_url && !item.previewing" class="btn btn-warning" aria-label="Preview Track">
                                    <span class="glyphicon glyphicon-play"></span>
                                </a>
                                <a ng-click="pausePreview(item)" ng-if="item.previewing" class="btn btn-warning" aria-label="Preview Track">
                                    <span class="glyphicon glyphicon-stop"></span>
                                </a>
                                <a ng-click="upVote(item)" ng-if="!item.voted" class="btn btn-success" aria-label="Preview Track">
                                    <span class="glyphicon glyphicon-arrow-up"></span>
                                </a>
                            </div>

                            <ul class="list-inline votes">
                                <li ng-repeat="vote in item.votes">
                                    <img width="25" ng-src="{{ vote.photo }}" alt="{{ vote.name }}" />
                                </li>
                            </ul>

                            <h4 class="media-heading">{{ item.title }}</h4>
                            <p>{{ item.artist }}</p>
                            <small class="text-muted" title="{{ item.added_on_formatted }}">
                                {{ item.added_on_relative }} by {{ item.added_by.first_name }}
                            </small>

                            <a ng-click="deleteTrack(item)" style="display:none;" class="btn btn-xs btn-danger delete-track" aria-label="Add Track">
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div ng-controller="SearchController" class="col-md-4" id="search-pane">
            <form name="search" ng-submit="searchForTracks()">
                <div ng-class="{'form-group': results.length === 0, 'input-group': results.length > 0}">
                    <input required name="query" autocomplete="off" placeholder="Search" class="form-control" ng-model="query" />
                    <span class="input-group-btn" ng-if="results.length">
                        <button class="btn btn-default" type="button" ng-click="clearSearch()">
                            <span class="glyphicon glyphicon-remove"></span>
                        </button>
                    </span>
                </div>
            </form>
            <div class="search-results" ng-repeat="result in results">
                <h4>{{ result.provider }} <span class="badge">{{ result.items.length }}</span></h4>
                <ul class="list-group media-list">
                    <li class="media list-group-item" style="margin-top:0;" ng-repeat="item in result.items">
                        <div class="media-left">
                            <img class="media-object" width="75" height="75" ng-src="{{ item.art.thumbnail }}" />
                        </div>
                        <div class="media-body">
                            <div class="btn-group-vertical btn-group-xs pull-right" role="group">
                                <a href="{{ item.url }}" target="_blank" class="btn btn-warning" aria-label="Permalink">
                                    <span class="glyphicon glyphicon-link"></span>
                                </a>
                                <a ng-click="playPreview(item)" ng-if="item.preview_url && !item.previewing" class="btn btn-warning" aria-label="Preview Track">
                                    <span class="glyphicon glyphicon-play"></span>
                                </a>
                                <a ng-click="pausePreview(item)" ng-if="item.previewing" class="btn btn-warning" aria-label="Preview Track">
                                    <span class="glyphicon glyphicon-pause"></span>
                                </a>
                                <a ng-click="addTrack(item)" class="btn btn-warning" aria-label="Add Track">
                                    <span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </div>
                            <h4 class="media-heading">{{ item.title }}</h4>
                            <p>{{ item.artist }}</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="/js/components/angular/angular.min.js"></script>
    <script type="text/javascript" src="/js/components/lodash/lodash.min.js"></script>
    <script type="text/javascript" src="/js/components/angular-spotify/dist/angular-spotify.min.js"></script>
    <script type="text/javascript" src="/js/components/angular-audio/app/angular.audio.js"></script>
    <script type="text/javascript" src="/js/components/firebase/firebase.js"></script>
    <script type="text/javascript" src="/js/components/moment/min/moment.min.js"></script>
    <script type="text/javascript" src="//connect.soundcloud.com/sdk.js"></script>
    <script type="text/javascript" src="/js/bundle.js"></script>
</body>
</html>
