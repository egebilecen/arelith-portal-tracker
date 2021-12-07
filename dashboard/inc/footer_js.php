<script>
    function hide_search_results()
    {
        $(".search-results").css("display", "none");
    }

    function display_search_results(data=null, type=null)
    {
        if(data !== null && type !== null)
        {
            let view_func_str = (type == "character") ? 
                                "view_character_page" : "view_player_page";

            let html = "<tr onclick=\""+view_func_str+"('$func_result')\"><td>$result</td></tr>";

            $("#search-results-table > tbody").html("");

            for(let i=0; i < data.length; i++)
            {
                let result = data[i];
                $("#search-results-table > tbody").append(html.replace("$result", result).replace("$func_result", encodeURI(result).replaceAll("'", "\\'")));
            }
        }

        $(".search-results").css("display", "block");
    }

    function view_player_page(name)
    {
        // console.log("Player name: "+name);
        window.location = "view_player.php?name="+name;
    }

    function view_character_page(name)
    {
        // console.log("Character name: "+name);
        window.location = "view_character.php?name="+name;
    }

    let SEARCH_AJAX_BLOCK = false;

    (function () {
        feather.replace({ 'aria-hidden': 'true' });
    })();

    $(() => {
        $("input[name='search']").on("input", () => {
            if(SEARCH_AJAX_BLOCK) return;

            let search_type = $("select[name='search_type']").val();
            let val         = $("input[name='search']").val();

            if(!val)
            {
                hide_search_results();
                return;
            }

            SEARCH_AJAX_BLOCK = true;

            $.ajax({
                url    : "ajax.php",
                method : "post",
                data   : {
                    search : val,
                    type   : search_type
                },
                success : (data) => {
                    let data_json = JSON.parse(data);

                    if(data_json.data.length > 0)
                        display_search_results(data_json.data, data_json.type);
                    else 
                        hide_search_results();

                    SEARCH_AJAX_BLOCK = false;
                },
                error : (err) => {
                    SEARCH_AJAX_BLOCK = false;
                    alert("An AJAX error occured. ("+err.status+" - "+err.statusText+")");
                }
            });
        });
    });
</script>