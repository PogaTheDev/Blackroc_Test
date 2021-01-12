

<?php 
    /////////////////////////////////////////////////////////////////
    // NOTES:
    // The api returns 10 results per page and it appears you have
    // select the page specifically if you want anything after
    // page one. I decided for this test that i would only use
    // the first page (per colour) as i assumed you
    // didn't want 10,000+ results.
    // 
    // While requested in the email, runtime doesnt appear to be a  
    // value returned by the API, I have chosen just to just display 
    // title and year and type.
    // 
    // I have also decided to include all media types: 
    // (movies, series and episodes). While the email did say Movies
    // I interprited this to mean and result from the 
    // "Movie Database" given.
    //
    ////////////////////////////////////////////////////////////////

    // http://inexplainable-jug.000webhostapp.com/

    // Used to store all movie objects
    $movies = [];
    // Used to store movie titles to remove repeat entries into $movies
    $movieTitles = [];
    // The colours used to search the api
    $colours = ['red', 'green', 'blue', 'yellow'];
    // Used to change the hex values associated with the colours in the movies
    $colourHex = ['red'=>'#ffc1b8', 'green'=>'#d3ffb8', 'blue'=>'#b8ecff', 'yellow'=>'#fffab8'];

    // Find all the relevent movies
    foreach($colours as $colour) {
        // Get the api return for the given colour
        $apiResponse = file_get_contents('http://www.omdbapi.com/?apikey=9e7cf38&s='.$colour);
        // Decode the raw json
        $decodedResponse = json_decode($apiResponse);
        // Loop through the indervidual movies within the return and add them to the main list
        foreach($decodedResponse->Search as $movie) {
            // Check to see if the title of the movie doesnt already exists within our set
            if(!in_array($movie->Title, $movieTitles)) {
                $movieTitle = $movie->Title;
                // Add the title to the list of titles for future redundency checks
                array_push($movieTitles, $movieTitle);
                // Remove non-alphaneumeric characters from the title
                $movieTitle = preg_replace("/[^A-Za-z0-9 ]/", '', $movieTitle);
                // Break the title up into component words
                $movieTitleWords = explode(' ', $movieTitle);
                // Check to see if any of the words match (starting from first word)
                foreach($movieTitleWords as $movieTitleWord) {
                    foreach($colours as $colour) {
                        if (strtolower($colour) == strtolower($movieTitleWord)) {
                            // Add that colour as a variable into the movie object before adding it to the main list
                            $movie->Colour = $colourHex[strtolower($colour)];
                            break 2;
                        }
                    }
                }
                // Add the movie to the main list of movies
                array_push($movies, $movie);
            }
        }
    }

    // Sort the list Alphabetically
    usort($movies, "alphaSort");

    // Alphabetical sorting function to be used with movie std class
    function alphaSort($a, $b){
        $compareResult = strcasecmp($a->Title, $b->Title);

        if($compareResult < 0) {
            return -1;
        }

        if($compareResult >= 0) {
            return 1;
        }
        
        return $compareResult;
    }
?>

<style>

    h1{
        text-align: center;
    }

    h3{
        text-align: center;
    }

    td{
        text-align: center;
        border-bottom: #c2c2c2 1px solid;
    }

    .page{
        
    }

    .center {
        margin: auto;
        width: 40%;
    }

    .movie_table {
        background-color: #c2c2c2;
        width:100%;
        border-spacing: 0px;
    }
</style>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Rainbow</title>
</head>
<body>
    <div class="center page">
        <h1>Mitchell's Movie Rainbow</h1>
        <h3>Brought to you by <a href="http://www.omdbapi.com/">OMDb</a></h3>
        <div>
            <table class="movie_table">
                <tr>
                    <th>Title</th>
                    <th>Year</th>
                    <th>Media Type</th>
                </tr>
                <?php foreach($movies as $movie): ?>
                <tr style="background-color:<?php echo $movie->Colour; ?>">
                    <td>
                        <?php echo $movie->Title ?>
                    </td>
                    <td>
                        <?php echo $movie->Year ?>
                    </td>
                    <td>
                        <?php echo $movie->Type ?>
                    </td>
                </tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>
</body>
</html>