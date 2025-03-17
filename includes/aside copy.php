<aside class="main-sidebar">
  <section class="sidebar">
    <ul class="sidebar-menu" data-widget="tree">

      <?php $menu_permission = explode('||', $_SESSION["menu_permission"]); ?>
      <!-- <li class="header">MAIN NAVIGATION</li> -->

      <li id="dashboardMainNav">
        <a href="index.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
      </li>

      <li class="treeview" id="transMainNav">
        <a href="#">
          <i class="fa fa-desktop"></i>
          <span>Transaction</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li id="transSalesSubNav"><a href="sales_lst.php">Sales</a></li>
        </ul>
      </li>
      
      <li class="treeview" id="masterMainNav">
        <a href="#">
          <i class="fa fa-edit"></i>
          <span>Masters</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li id="masterSupplierSubNav"><a href="supplier.php">Supplier</a></li>
          <li id="masterCategorySubNav"><a href="category.php">Category</a></li>
          <li id="masterCustomerSubNav"><a href="customer.php">Customer</a></li>
          <li id="masterGstSubNav"><a href="gst.php">GST</a></li>
          <li id="masterProductSubNav"><a href="product_lst.php">Product</a></li>
          <li id="masterTransportSubNav"><a href="transport.php">Transport</a></li>
          <li id="masterUOMSubNav"><a href="uom.php">UOM</a></li>
        </ul>
      </li>

      <?php if(in_array('mnuRolesPermissions',$menu_permission) || in_array('mnuUsers',$menu_permission) || in_array('mnuShop',$menu_permission)){ ?>
      <li class="treeview" id="settingsMainNav">
        <a href="#">
          <i class="fa fa-cog"></i>
          <span>Settings</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <?php 
          if(in_array('mnuRolesPermissions',$menu_permission)){
            echo '<li id="settingsRolesSubNav"><a href="roles_lst.php">Roles and Permissions</a></li>'; 
          }
          if(in_array('mnuUsers',$menu_permission)){
            echo '<li id="settingsUsersSubNav"><a href="users_lst.php">Users</a></li>'; 
          }
          if(in_array('mnuShop',$menu_permission)){
            echo '<li id="settingsShopSubNav"><a href="shop.php">Shop Details</a></li>';
          }
          ?>
        </ul>
      </li>
      <?php } ?>

    </ul>
  </section>
</aside>