
<?php include('db_connect.php');?>

<div class="container-fluid">
    
    <div class="col-lg-12">
        <div class="row mb-4 mt-4">
            <div class="col-md-12">
                
            </div>
        </div>
        <div class="row">
            <!-- FORM Panel -->

            <!-- Table Panel -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <b>Register Tenant</b>
                        <span class="float:right"><a class="btn btn-primary btn-block btn-sm col-sm-2 float-right" href="index.php?page=tenants">
                    <i class="fa fa-list"></i> All Tenants
                </a></span>
                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <form action="register_tenant_code.php" method="POST">
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="" class="control-label">First Name</label>
                                        <input type="text" class="form-control" name="firstname" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="" class="control-label">Middle Name</label>
                                        <input type="text" class="form-control" name="middlename">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="" class="control-label">Last Name</label>
                                        <input type="text" class="form-control" name="lastname" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label for="" class="control-label">Email</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="" class="control-label">Contact #</label>
                                        <input type="text" class="form-control" name="contact" required>
                                        <?php 
                                            $length = 8;    
                                            $password =  substr(str_shuffle('0stuvw123qrxyz456lmnop789abcdefqhijk'),1,$length);
                                        ?>
                                        <input type="hidden" name="password" value="<?php echo $password; ?>" class="form-control">
                                    </div>              
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label for="" class="control-label">Apartment No</label>
                                        <select name="apartment_id" id="" class="custom-select select2" required>
                                            <option value=""></option>
                                            <?php 
                                            $apartment = $conn->query("SELECT * FROM apartments where id not in (SELECT apartment_id from tenantapartment where status = 1) ".(isset($apartment_id)? " or id = $apartment_id": "" )." ");
                                            while($row= $apartment->fetch_assoc()):
                                            ?>
                                            <option value="<?php echo $row['id'] ?>" <?php echo isset($apartment_id) && $apartment_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['apartment_no'] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="" class="control-label">Registration Date</label>
                                        <input type="date" class="form-control" name="date_in"  required>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <input class="btn btn-primary btn-sm btn-block" name="register_tenant" type="submit" value="Register" id="enable_desable_btn">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Table Panel -->
        </div>
    </div>  

</div>
<style>
    
    td{
        vertical-align: middle !important;
    }
    td p{
        margin: unset
    }
    img{
        max-width:100px;
        max-height: :150px;
    }
</style>

