<div class="sidebar" id="sidebar">
    <div class="sub-sidebar">
        <h2 class="logo" id="logo">IMS</h2>
        <div class="user">
            <img src="public/images/user.jpeg" alt="User">
            <span style="text-transform: uppercase;">
                <?php
                    echo $_SESSION['username'];
                 ?></span>
        </div>
    </div>
    <div class="divider"></div>
    <div class="menu-container">
        <ul class="menu">
            <li class="liMainMenu" title="Dashboard"><a href="./dashboard.php">
                    <i class="fa fa-dashboard"></i><span class="menu-text"> Dashboard</a></span>
            </li> 
            <li class="liMainMenu" title="Report"><a href="./report.php">
                    <i class="fa fa-file"></i><span class="menu-text"> Report</a></span>
            </li>
            <li class="liMainMenu" title="Myorders"><a href="./my-orders.php">
                    <i class="fa fa-shopping-cart"></i><span class="menu-text"> My Order</a></span>
            </li>
            <li class="liMainMenu" title="Product"><a href="#"><i class="fa-solid fa-tag"></i>
                    <span class="menu-text liSubMenu_Link"> Product
                        <i class="fa fa-angle-left leftArrowIcon liSubMenu_Link"></i>
                    </span></a>
                <ul class="Menu subMenu">
                    <li><a href="./product-add.php" class=""><i class="fa fa-circle-o subMenuIcon"></i><span>Add Product</span></a></li>
                    <li><a href="./product-view.php" class=""><i class="fa fa-circle-o subMenuIcon"></i><span>View Products</span></a></li>
                
                </ul>
            </li>
            <li class="liMainMenu" title="Supplier"><a href="#"><i class="fa fa-truck"></i>
                    <span class="menu-text liSubMenu_Link"> Supplier
                        <i class="fa fa-angle-left leftArrowIcon liSubMenu_Link"></i>
                    </span></a>
                <ul class="Menu subMenu">
                    <li><a href="./supplier-add.php" class=""><i class="fa fa-circle-o subMenuIcon"></i><span>Add Suppliers</span></a></li>
                    <li><a href="./supplier-view.php" class=""><i class="fa fa-circle-o subMenuIcon"></i><span>View Supplier</span></a></li>
                
                </ul>
            </li>
             <li class="liMainMenu" title="Product Order"><a href="#"><i class="fa fa-shopping-cart"></i>
                    <span class="menu-text liSubMenu_Link"> Purchase Order
                        <i class="fa fa-angle-left leftArrowIcon liSubMenu_Link"></i>
                    </span></a>
                <ul class="Menu subMenu">
                    <li><a href="./product-order.php" class=""><i class="fa fa-circle-o subMenuIcon"></i><span>Create Order</span></a></li>
                    <li><a href="./view-orders.php" class=""><i class="fa fa-circle-o subMenuIcon"></i><span>view orders</span></a></li>
                </ul>
            </li>
            <li class="liMainMenu" title="User"><a href="#"><i class="fa-solid fa-user-plus"></i>
                    <span class="menu-text liSubMenu_Link"> User
                        <i class="fa fa-angle-left leftArrowIcon liSubMenu_Link"></i>
                    </span></a>
                <ul class="Menu subMenu">
                    <li><a href="./user-add.php" class=""><i class="fa fa-circle-o subMenuIcon"></i><span>Add User</span></a></li>
                    <li><a href="./user-view.php" class=""><i class="fa fa-circle-o subMenuIcon"></i><span>View User</span></a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>