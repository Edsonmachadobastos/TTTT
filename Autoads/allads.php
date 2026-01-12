<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logo texto Baixo Direita</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            position: relative; /* Added to position the backdrop and logo relative to the body */
            height: 100vh;
            background-color: #222;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #backdrop {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .logo-container {
            position: absolute;
            bottom: 20px; /* Adjust top position as needed */
            right: 20px; /* Adjust left position as needed */
            z-index: 1; /* Ensures logo is above the backdrop */
            padding: 10px; /* Add padding to the logo container */
            background-color: rgba(0, 0, 0, 0.5); /* Background color with transparency */
            border-radius: 5px; /* Rounded corners */
            color: #fff; /* Text color */
        }
        .logo {
            max-width: 400px; /* Adjust size of the logo as needed */
        }
        .movie-info {
            max-width: 300px; /* Adjust maximum width of the movie info container */
            margin-top: 10px; /* Adjust margin top as needed */
            font-size: 14px; /* Adjust font size as needed */
        }
    </style>
</head>
<body>
    <img id="backdrop">
    <div class="logo-container">
        <img id="logo" class="logo">
        <div id="movie-info" class="movie-info"></div>
    </div>

    <script>
        const apiKey = '6b8e3eaa1a03ebb45642e9531d8a76d2'; // Replace with your TMDb API key
        let currentIndex = 0;
        let movieIds = [];

        async function fetchPopularEnglishMovieIds() {
            try {
                const response = await fetch(`https://api.themoviedb.org/3/discover/movie?api_key=${apiKey}&sort_by=popularity.desc&language=pt`);
                if (!response.ok) {
                    throw new Error('Failed to fetch popular English movies');
                }
                const data = await response.json();
                movieIds = data.results.map(movie => movie.id);
            } catch (error) {
                console.error('Error fetching popular English movies:', error);
            }
        }

        async function fetchMovieBackdrop(movieId) {
            try {
                const response = await fetch(`https://api.themoviedb.org/3/movie/${movieId}/images?api_key=${apiKey}`);
                if (!response.ok) {
                    throw new Error('Failed to fetch movie backdrops');
                }
                const data = await response.json();
                const backdropPath = data.backdrops[0].file_path; // Assuming the first backdrop in the array
                return `https://image.tmdb.org/t/p/original${backdropPath}`;
            } catch (error) {
                console.error('Error fetching movie backdrop:', error);
            }
        }

        async function fetchMovieLogo(movieId) {
            try {
                const response = await fetch(`https://api.themoviedb.org/3/movie/${movieId}?api_key=${apiKey}&append_to_response=images`);
                if (!response.ok) {
                    throw new Error('Failed to fetch movie logo');
                }
                const data = await response.json();
                const logos = data.images.logos;
                const englishLogo = logos.find(logo => logo.iso_639_1 === 'pt');
                if (englishLogo) {
                    return `https://image.tmdb.org/t/p/w500${englishLogo.file_path}`;
                } else {
                    return null;
                }
            } catch (error) {
                console.error('Error fetching movie logo:', error);
            }
        }

        async function fetchMovieInfo(movieId) {
            try {
                const response = await fetch(`https://api.themoviedb.org/3/movie/${movieId}?api_key=${apiKey}&language=pt`);
                if (!response.ok) {
                    throw new Error('Failed to fetch movie info');
                }
                const data = await response.json();
                return {
                    title: data.title,
                    releaseDate: data.release_date,
                    overview: data.overview
                };
            } catch (error) {
                console.error('Error fetching movie info:', error);
            }
        }

        async function updateBackdropAndLogo() {
            if (movieIds.length === 0) {
                console.error('No movie IDs available.');
                return;
            }

            const movieId = movieIds[currentIndex];

            try {
                const backdropUrl = await fetchMovieBackdrop(movieId);
                if (backdropUrl) {
                    const backdrop = document.getElementById('backdrop');
                    backdrop.src = backdropUrl;
                }

                const logoUrl = await fetchMovieLogo(movieId);
                if (logoUrl) {
                    const logo = document.getElementById('logo');
                    logo.src = logoUrl;
                }

                const movieInfo = await fetchMovieInfo(movieId);
                if (movieInfo) {
                    const movieInfoElement = document.getElementById('movie-info');
                    movieInfoElement.innerHTML = `
                        <strong>${movieInfo.title}</strong> (${movieInfo.releaseDate.substring(0, 4)})<br>
                        ${movieInfo.overview}
                    `;
                }

                currentIndex = (currentIndex + 1) % movieIds.length;
            } catch (error) {
                console.error('Error updating backdrop, logo, and movie info:', error);
            }
        }

        fetchPopularEnglishMovieIds().then(() => {
            setInterval(updateBackdropAndLogo, 5000); // Change backdrop, logo, and movie info every 6 seconds (adjust as needed)
            updateBackdropAndLogo(); // Initial update
        });
    </script>
</body>
</html>

