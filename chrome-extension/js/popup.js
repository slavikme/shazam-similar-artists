var ShazaSimilarArtists = {
    simlarUrl: "http://ssal.slavik.meltser.info/findsimilar.php",
    baseUrl: "http://www.shazam.com",
    artists: [],
    nextPath: "/fragment/myshazam",
    shazam_artist_id_map: {},
    getArtistsData: function(callback){
        var _this = this;
        $.ajax({
            url: this.baseUrl + this.nextPath,
            success: function(data){
                if ( typeof data != "object" || !("feed" in data) ) {
                    _this.raiseError("Unable to load your songs data from your Shazam acount.");
                }
                _this.nextPath = data.next;
                callback.call(_this, _this.renderDataFromFeedHTML(data.feed));
            },
            failed: function() {
                _this.raiseError("Connection with Shazam was failed. Please make sure you're logged-in to Shazam.");
            }
        });
    },
    renderDataFromFeedHTML: function(html) {
        var _this = this;
        var found_new_artists = [];
        $(html).each(function(){
            var $this = $(this);
            if ( !$this.hasClass("tl-container") ) {
                return;
            }

            var song_link = $this.find(".tl-cover-art > a").attr("href");
            var song_id = parseInt(song_link.match(/\d+$/)[0]);
            var image = $this.find(".tl-cover-art > a > img").attr("src");
            var song_name = $this.find(".tl-title > a").text().trim();

            var $artist = $this.find(".tl-artist a");
            var artist_link = $artist.attr("href");
            var artist_id = parseInt(artist_link.match(/\d+$/)[0]);
            var artist_name = $artist.text().trim();

            var found = _this.getArtistByShazamArtistId(artist_id);
            var songs_obj = {
                shazam: { id: song_id, link: song_link },
                name: song_name,
                image: image
            };
            if ( found ) {
                found.shazam.songs.push(songs_obj);
                _this.outputUpdateArtist(found);
                return;
            }
            var artist_obj = {
                shazam: { id: artist_id, link: artist_link, songs: [songs_obj] },
                name: artist_name,
                similar: [],
                _index: _this.artists.length,
                _songs_folded: true,
                _similar_folded: true,
                _similar_load_locked: false,
                _similar_loaded: false
            };
            _this.shazam_artist_id_map[artist_id] = _this.artists.push(artist_obj) - 1;
            found_new_artists.push(artist_obj);
        });
        return found_new_artists;
    },
    getArtistByShazamArtistId: function(shazam_artist_id){
        if ( shazam_artist_id in this.shazam_artist_id_map ) {
            return this.artists[this.shazam_artist_id_map[shazam_artist_id]];
        }
    },
    raiseError: function(message){
        console.error(message);
    },
    showMainLoader: function(){
        this.hideMainLoader();
        $("<div/>").attr({id:"main-loader"}).text("Retrieving artists, please wait...").appendTo("body");
    },
    hideMainLoader: function(){
        $("#main-loader").remove();
    },
    outputAppendArtist: function(artist_data) {
        var $artist = $(Handlebars.templates.artist(artist_data));
        this.attachEvents($artist, artist_data);
        $artist.appendTo("#artist-container");
    },
    outputAppendArtists: function(artist_data_list) {
        artist_data_list = artist_data_list || [];
        for ( var i=0; i<artist_data_list.length; i++ ) {
            this.outputAppendArtist(artist_data_list[i]);
        }
    },
    outputUpdateArtist: function(artist_data) {
        var $new = $(Handlebars.templates.artist(artist_data));
        this.attachEvents($new, artist_data);
        $(".artist-row[data-id="+artist_data._index+"]").replaceWith($new);
    },
    attachEvents: function($artist, artist_data) {
        var _this = this;
        $artist.find(".artist-detail-heading").on("click",function(){
            if ( $(this).hasClass("opened") ) {
                $(this).removeClass("opened").next().addClass("hidden");
                if ( $(this).find(".artist-similar-more").length ) {
                    artist_data._similar_folded = true;
                }
                if ( $(this).find(".artist-songs-more").length ) {
                    artist_data._songs_folded = true;
                }
            } else {
                $(this).addClass("opened").next().removeClass("hidden");
                if ( $(this).find(".artist-similar-more").length ) {
                    artist_data._similar_folded = false;
                }
                if ( $(this).find(".artist-songs-more").length ) {
                    artist_data._songs_folded = false;
                }
            }
            if ( !artist_data._similar_load_locked && !artist_data._similar_loaded ) {
                artist_data._similar_load_locked = true;
                $.ajax({
                    url: _this.simlarUrl + "?szid=" + artist_data.shazam.id + "&name=" + encodeURIComponent(artist_data.name),
                    success: function(data){
                        artist_data.similar = data;
                        artist_data._similar_loaded = true;
                        _this.outputUpdateArtist(artist_data);
                    },
                    error: function() {
                        _this.raiseError("Unable to retrieve similar artists.");
                    },
                    complete: function() {
                        artist_data._similar_load_locked = false;
                    }
                });
            }
        });
    }
};

$(function(){
    var startUpUrl = "http://www.shazam.com/myshazam";
    chrome.tabs.getSelected(function(tab){
        if ( tab.url.indexOf(startUpUrl) ) {
             chrome.tabs.update({url:startUpUrl});
        }
    });
   
    ShazaSimilarArtists.showMainLoader();
    ShazaSimilarArtists.getArtistsData(function(artists){
        this.hideMainLoader();
        this.outputAppendArtists(artists);
    });
});