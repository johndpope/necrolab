<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SteamUsersResource extends JsonResource {
    /**
     * Transforma single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $steamid = (string)$this->steamid;
    
        $record = [
            'id' => $steamid
        ];
        
        if(!empty($this->necrolab_id)) {
            $record['necrolab'] = [
                'id' => $steamid,
                'username' => $this->necrolab_username
            ];
        }
        
        $record['steam'] = [
            'id' => $steamid,
            'username' => $this->steam_username,
            'profile_url' => $this->steam_profile_url
        ];
        
        if(!empty($this->mixer_id)) {
            $record['mixer'] = [
                'id' => $this->mixer_id,
                'username' => $this->mixer_username,
            ];
        }
        
        if(!empty($this->discord_id)) {
            $record['discord'] = [
                'id' => $this->discord_id,
                'username' => $this->discord_username,
                'discriminator' => $this->discord_discriminator
            ];
        }
        
        if(!empty($this->reddit_id)) {
            $record['reddit'] = [
                'id' => $this->reddit_id,
                'username' => $this->reddit_username,
            ];
        }
        
        if(!empty($this->twitch_id)) {
            $record['twitch'] = [
                'id' => $this->twitch_id,
                'username' => $this->twitch_username,
            ];
        }
        
        if(!empty($this->twitter_id)) {
            $record['twitter'] = [
                'id' => $this->twitter_id,
                'nickname' => $this->twitter_nickname,
                'name' => $this->twitter_name
            ];
        }
        
        if(!empty($this->youtube_id)) {
            $record['youtube'] = [
                'id' => $this->youtube_id,
                'username' => $this->youtube_username,
            ];
        }
    
        return $record;
    }
}
