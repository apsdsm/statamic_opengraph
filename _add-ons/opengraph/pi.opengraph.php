<?php

/**
 * Plugin_opengraph
 * adds opengraph and meta tags to the top of your pages so that they work reasonably with Twitter, Facebook, Google+, etc.
 */
class Plugin_opengraph extends Plugin {
    
    function __construct(){
        parent::__construct();
        $this->page = Content::get(URL::getCurrent());

        // get control and location variables
        $this->ignore_seo_image_field = $this->fetchConfig('ignore_seo_image_field');
        $this->sharable_image_default = $this->fetchConfig('default_image');
        $this->sharable_image_source = $this->fetchConfig('sharable_image_source');

        // get the variables that are actually printed in output
        $this->site_url = URL::getSiteURL();
        $this->page_url = URL::tidy($this->site_url . URL::getCurrent(true));
        $this->description = $this->getPageDescription();
        $this->twitter = $this->fetchConfig('twitter');
        $this->twitter_default_message = $this->fetchConfig('twitter_default_message');
        $this->site_name = Config::getSiteName();
        $this->image_url = $this->getShareableImage();
        $this->page_title = $this->site_name . " | " . $this->getPageTitle();
        $this->google_analytics = $this->fetchConfig('google_analytics_key');
    }

    /**
     * Will return the page description.
     */
    private function getPageDescription(){
        return isset($this->page['og_description']) ? $this->page['og_description'] : $this->fetchConfig('default_description');
    }
    
    /**
     * Will return the page title.
     */
    private function getPageTitle(){
        return isset($this->page['og_title']) ? $this->page['og_title'] : $this->page['title'];
    }
    
    /**
     * Will return the sharable image for the page.
     */
    private function getShareableImage(){

        try {
            
            // if an image variable was passed directly
            if ($this->fetch('image')) {
                return $this->fetch('image');                
            }

            // if there was an og image specified on the page, use it (or get first image from array)
            if (isset($this->page['og_image'])) {
                $image = "";

                if (is_array($this->page['og_image'])) {
                    $image = $this->page['og_image'][0];
                } else {
                    $image = $this->page['og_image'];
                }

                return URL::assemble($this->site_url, $image);
            }

            // if another image is specified as the source
            if ($this->shareable_image_source && isset($this->page[$this->sharable_image_source])){

                if (is_array($this->page[$this->sharable_image_source])) {
                    $image = $this->page[$this->sharable_image_source][0];
                } else {
                    $image = $this->page[$this->sharable_image_source];
                }

                return URL::assemble($this->site_url, $image);
            }

            // if there's a default image
            if ($this->sharable_image_default){
                return $this->sharable_image_default;
            }

            // and if all else fails return an empty string
            return "";
            
        }

        catch (Exception $e) {
            return "<!-- Exception occured: " . $e->getMessage() . " -->";
        }
    }

    /**
     * Displays the code to link the current page to your Google Analytics. Call from the page header someplace.
     */
    public function analytics(){
        $output = "
        <!-- Google Analytics -->
        <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', '{$this->google_analytics}', 'auto');
        ga('send', 'pageview');
        </script>
        ";

        return $output;
    }
    
    /**
     * sharebuttons
     * Displays share buttons that use the values that were set for the opengraph tags. Call this from somewhere in your page body.
     */
    public function sharebuttons(){
        try 
        {
            $twitter_message = $this->fetch('twitter_message');
            $encoded_url = urlencode($this->page_url);

            // start building the output
            $output = "<!--Start Share Buttons-->";

            // open button list
            $output .= '<ul class="share-buttons">';

            // Twitter
            //
            $href = "https://twitter.com/intent/tweet?" . http_build_query(array(
                'source' => $encoded_url,
                'text' => $this->page_url . " ". $this->twitter_default_message . " via: @" . $this->twitter,
                
            ));
            
            $output .= "<li><a class=\"social-link twitter\" href=\"{$href}\" title=\"Share on Twitter\" target=\"blank\">";
            $output .= "<i class=\"fa fa-twitter fa-2x\"></i>";
            $output .= "</a></li>";


            // Google+
            //
            $href = "https://plus.google.com/share?" . http_build_query(array(
                'url' => $encoded_url
            ));
            
            $output .= "<li><a class=\"social-link google\" href=\"{$href}\" title=\"Share on Google\" target=\"blank\">";
            $output .= "<i class=\"fa fa-google-plus fa-2x\"></i>";
            $output .= "</a></li>";
          

            // Facebook
            //
            $href = "https://www.facebook.com/sharer/sharer.php?" . http_build_query(array(
                'u' => $encoded_url,
                't' => ''
            ));
            
            $output .= "<li><a class=\"social-link facebook\" href=\"{$href}\" title=\"Share on Facebook\" target=\"blank\">";
            $output .= "<i class=\"fa fa-facebook fa-2x\"></i>";
            $output .= "</a></li>";


            // Pinterest
            //
            $href = "http://pinterest.com/pin/create/button?" . http_build_query(array(
                'url' => $encoded_url,
                'description' => $this->description,
                'media' => $this->image_url
            ));
            
            $output .= "<li><a class=\"social-link pinterest\" href=\"{$href}\" title=\"Share on Pinterest\" target=\"blank\" >";
            $output .= "<i class=\"fa fa-pinterest fa-2x\"></i>";
            $output .= "</a></li>";

            
            // (As soon as Font Awesome has a Pocket icon, you can bet this is uncommented)
            // Pocket
            //
            // $href = "https://getpocket.com/save?" . http_build_query(array(
            //     'url' => $encoded_url,
            //     'title' => $this->page_title
            // ));
            //
            // $output .= "<li><a class=\"social-link pocket\" href=\"{$href}\" title=\"Save to Pocket\" target=\"blank\" >";
            // $output .= "<i class=\"fa fa-pocket fa-2x\"></i>";
            // $output .= "</a></li>";

            // close button list
            $output .= "</ul>";
            
            return $output;
        } 
        catch (Exception $e) 
        {
            return "<!-- There was an error in the opengraph plugin! - " . $e->getMessage() . " -->";
        }
        
    }
    
    /**
     * Displays all the opengraph meta data for the current page. If that data doesn't exist, it uses defaults set in the 
     * plugin config. Call this from inside the header of your layout.
     */
    public function metatags(){
        try 
        {
            // start building the output
            $output = "<!--Start OpenGraph Settings-->";
            
            // add Google+ stuff
            $output .= '<!-- Schema.org markup for Google+ -->' .
                       '<meta itemprop="name" content="' . $this->page_title . '">' .
                       '<meta itemprop="description" content="' . $this->description . '">';

            if ($this->image_url !== "") {
                $output .= '<meta itemprop="image" content="' . $this->image_url . '">';
            }

            // add Twitter Stuff
            $output .= '<!-- Twitter Card data -->' .
                       '<meta name="twitter:card" content="summary">' .
                       '<meta name="twitter:site" content="@' . $this->twitter . '">' .
                       '<meta name="twitter:title" content="' . $this->page_title . '">' .
                       '<meta name="twitter:description" content="' . $this->description . '">' .
                       '<meta name="twitter:creator" content="@' . $this->twitter  . '">';

            if ($this->image_url !== "") {
                $output .= '<meta name="twitter:image" content="' . $this->image_url . '">';
            }

            // add OpenGraph Stuff
            $output .= '<!-- Open Graph data -->' .
                       '<meta property="og:title" content="' . $this->page_title . '" />' .
                       '<meta property="og:type" content="article" />' .
                       '<meta property="og:url" content="' . $this->page_url . '" />' .
                       '<meta property="og:description" content="' . $this->description . '" />' .
                       '<meta property="og:site_name" content="' . $this->site_name . '" />';

            if ($this->image_url !== "") {
                $output .= '<meta property="og:image" content="' . $this->image_url . '" />';
            }

            // all done!
            return $output;
            
        }
        catch (Exception $e) 
        {
            return "<!-- There was an error in the opengraph plugin! - " . $e->getMessage() . " -->";
        }
    }
}
