if(Cookies.get('theme')=='dark'){
    darkThemeToggle();
}

function darkThemeToggle()
{
    let cookie_opts = {expires:1000};
    let css_dark = document.getElementsByClassName('dark_theme');
    let ln = css_dark.length;
    if(ln>0) {
        Cookies.set('theme','light',cookie_opts);
        for(var i=0; i<ln; i++){
            css_dark[0].remove();
        }
        return 'light';
    }
    Cookies.set('theme','dark',cookie_opts);

    let styles = [
        //'/css/bootstrap.dark.css',
        '/css/dark_modifier.css',
    ];

    let head = document.getElementsByTagName('head')[0];
    for(var i in styles){
        let link = document.createElement('link');
        link.setAttribute('href',styles[i]);
        link.setAttribute('rel','stylesheet');
        link.setAttribute('class','dark_theme');
        head.appendChild(link);
    }
    return 'dark';
}

function changeChartsTheme(theme)
{
    //this method is not working, now is it will check every 300ms theme and change if different (vendors/onmotion/..apexcharts)
    if(typeof ApexCharts == 'undefined') return;

    $("div[id*='apexcharts']").each((k,v)=>{
        var id = $(v).attr('id').replace(/^apexcharts/,'')

        ApexCharts.exec(id,'updateOptions',{
            theme: {
                mode : theme
            },
            chart: {
                background: 'transparent'
            },
            grid: {
                borderColor: theme=='dark'?'#a1a7ad':'#ccd2d9'
            }
        },true,false,true);
    });
}


window.addEventListener('DOMContentLoaded', (event) => {
    if(Cookies.get('theme')=='dark'){
        $('.theme_switch').addClass('dark');
    }
    $(document).on('click','.theme_switch',()=> {
        $('.theme_switch').toggleClass('dark');
        var theme = darkThemeToggle();
        changeChartsTheme(theme);
    });
});



