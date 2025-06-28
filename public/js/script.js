var sidebarIsOpen = true;

function toggleSidebar() {
    var sidebar = document.getElementById("sidebar"); // sidebar should have id="sidebar"
    var menuTexts = document.querySelectorAll(".menu-text");
    var icons = document.querySelectorAll(".menu i"); // target icons inside menu

    if (sidebarIsOpen) {
        sidebar.style.width = "15%";
        sidebar.style.transition = "0.4s all";
        logo.style.fontSize = "60px";

        // Hide text
        for (var i = 0; i < menuTexts.length; i++) {
            menuTexts[i].style.display = "none";
        }

        // Center icons
        for (var i = 0; i < icons.length; i++) {
            icons[i].style.margin = "0 auto";
            icons[i].style.display = "block";
            icons[i].style.textAlign = "center";
        }

        sidebarIsOpen = false;
    } else {
        sidebar.style.width = "25%";
        logo.style.fontSize = "80px";

        // Show text
        for (var i = 0; i < menuTexts.length; i++) {
            menuTexts[i].style.display = "inline";
        }

        // Reset icon style
        for (var i = 0; i < icons.length; i++) {
            icons[i].style.margin = "";
            icons[i].style.display = "inline-block";
            icons[i].style.textAlign = "";
        }

        sidebarIsOpen = true;
    }
}

document.addEventListener('click', function (event) {
    //let clcikkedEi = event.target;
    //console.log(event.target);

    if (event.target.classList.contains('liSubMenu_Link')) {
        event.preventDefault();
        //alert("You clicked a submenu link!");
        var submenu = event.target.closest('li').querySelector('.submenu');
        var liSubMenu_Link = event.target.querySelector('.liSubMenu_Link');
        // console.log(liSubMenu_Link);
        showHideSubMenu(submenu, liSubMenu_Link);

    }
})
function showHideSubMenu(subMenu, liSubMenu_Link) {
    if (subMenu != null && liSubMenu_Link) {
        if (subMenu.style.display === "block") {
            subMenu.style.display = "none";
            liSubMenu_Link.classList.remove('fa-angle-left', 'fa-angle-down');
            liSubMenu_Link.classList.add('fa-angle-right');
        } else {
            subMenu.style.display = "block";
            liSubMenu_Link.classList.remove('fa-angle-left', 'fa-angle-down');
            liSubMenu_Link.classList.add('fa-angle-down');
        }
    }
}
let pathArray = window.location.pathname.split('/');
let currPath = pathArray[pathArray.length - 1];
let currNav = document.querySelector('a[href="./' + currPath + '"]');
// currNav.classList.add('active');

let mainDiv = currNav.closest('.liMainMenu');
// console.log(mainDiv);
mainDiv.style.backgroundColor = '#f685a1';

// let subMenu = currNav.closest('.submenu');
// let liSubMenu_Link = currNav.querySelector('.liSubMenu_Link');
// showHideSubMenu(subMenu, liSubMenu_Link)
// console.log(showHideSubMenu(subMenu, liSubMenu_Link));





// const menuItems = document.querySelectorAll('.menu-item');

// menuItems.forEach(item => {
//     item.addEventListener('click', () => {

//         // Remove active from all first
//         menuItems.forEach(el => el.classList.remove('active'));

//         // Then add to clicked
//         item.classList.add('active');
//     });
// });

