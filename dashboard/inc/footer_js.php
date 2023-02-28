<script>
    function url_decode(url) 
    {
        return decodeURIComponent(url.replace(/\+/g, ' '));
    }
    
    function toggle_character_quick_access(character_name)
    {
        let character_list  = JSON.parse(localStorage.getItem("quick_access_characters"));
        let character_index = character_list.indexOf(character_name);

        if(character_index > -1)
            character_list.splice(character_index, 1);
        else
            character_list.push(character_name);
        
        localStorage.setItem("quick_access_characters", JSON.stringify(character_list));
        window.location.reload();
    }

    function clear_character_quick_access()
    {
        if(confirm("Are you sure to clear all characters in quick access?"))
        {
            localStorage.setItem("quick_access_characters", "[]");
            window.location.reload();
        }
    }

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
                $("#search-results-table > tbody").append(html.replace("$result", result).replace("$func_result", encodeURIComponent(result).replaceAll("'", "\\'")));
            }
        }

        $(".search-results").css("display", "block");
    }

    function view_player_page(name)
    {
        // console.log("Player name: "+name);
        window.location = "view_player?name="+name;
    }

    function view_character_page(name)
    {
        // console.log("Character name: "+name);
        window.location = "view_character?name="+name;
    }

    let SEARCH_AJAX_BLOCK = false;

    (function () {
        if(localStorage.getItem("quick_access_characters") === null)
            localStorage.setItem("quick_access_characters", "[]");
    })();

    (function () {
        feather.replace({ 'aria-hidden': 'true' });
    })();

    $(() => {
        let character_list = JSON.parse(localStorage.getItem("quick_access_characters"));

        if(character_list.length < 1)
        {
            $("#quick-access-characters").append("<li><span class='nav-link'>No data.</span></li>");
        }
        else
        {
            character_list.forEach(character_name => {
                $("#quick-access-characters").append("<li><a class='nav-link' href='view_character?name="+character_name+"'>"+url_decode(character_name)+"</span></li>");
            });
        }

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