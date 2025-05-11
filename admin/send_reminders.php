<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="container-fluid">
    
    <div class="col-lg-12">
        <div class="row mb-4 mt-4">
            <div class="col-md-12">
                
            </div>
        </div>
        <?php include('db_connect.php')?>
        <?php include('../code/send_reminders.php')?>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- <h4 class="header-title">Fill all fields</h4> -->
                        <form method="POST" action="">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="form-group col-md-6">
                                        <label for="">Payment Due Date</label>
                                        <input type="date" id="due_payment" name="due_payment" required class="form-control">
                                    </div>
                                    <button type="submit" name="send_reminders" class="ladda-button btn btn-primary" data-style="expand-right">Send Reminders</button>
                                </div>
                            </div>
                        </form>
                        <!--End Patient Form-->
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div>
    </div>
</div>
</body>
</html>