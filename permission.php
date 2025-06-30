<table>
    <tr>
        <td class="module">DASHBOARD</td>
        <td><span class="permission" data-value="dasboard-view">View</span></td>
        <td colspan="4"></td>
    </tr>

    <tr>
        <td class="module">REPORTS</td>
        <td><span class="permission" data-value="reports-view">View</span></td>
        <td colspan="4"></td>
    </tr>

    <tr>
        <td class="module">PURCHASE ORDER</td>
        <td><span class="permission" data-value="purchase-view">View</span></td>
        <td><span class="permission" data-value="purchase-create">Create</span></td>
        <td><span class="permission" data-value="purchase-edit">Edit</span></td>
        <td colspan="2"></td>
    </tr>

    <tr>
        <td class="module">PRODUCT</td>
        <td><span class="permission" data-value="product-view">View</span></td>
        <td><span class="permission" data-value="product-create">Create</span></td>
        <td><span class="permission" data-value="product-edit">Edit</span></td>
        <td><span class="permission" data-value="product-delete">Delete</span></td>
        <td></td>
    </tr>

    <tr>
        <td class="module">SUPPLIER</td>
        <td><span class="permission" data-value="supplier-view">View</span></td>
        <td><span class="permission" data-value="supplier-create">Create</span></td>
        <td><span class="permission" data-value="supplier-edit">Edit</span></td>
        <td><span class="permission" data-value="supplier-delete">Delete</span></td>
        <td></td>
    </tr>

    <tr>
        <td class="module">USER</td>
        <td><span class="permission" data-value="user-view">View</span></td>
        <td><span class="permission" data-value="user-create">Create</span></td>
        <td><span class="permission" data-value="user-edit">Edit</span></td>
        <td><span class="permission" data-value="user-delete">Delete</span></td>
        <td></td>
    </tr>

    <tr>
        <td class="module">POINT OF SALE</td>
        <td><span class="permission" data-value="pon">Has Access</span></td>
        <td colspan="4"></td>
    </tr>
</table>



<script>
    document.addEventListener('click', function(e) {
        let permissions = [];
        let target = e.target;
        if (target.classList.contains('permission')) {
            target.classList.toggle('permissionActive');
        }
        let permissionName = target.dataset.value;
        document.querySelectorAll('.permission.permissionActive').forEach(function(e) {
            let permissionValue = e.dataset.value;
            if (permissionValue) {
                permissions.push(permissionValue);
            }
            document.getElementById("permission_value").value=permissions.join(',');
        });
        
        //console.log(permissions);
    })
</script>