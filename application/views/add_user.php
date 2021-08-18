<!DOCTYPE html>
<html>
    <title>Register user</title>
    <body>

        <h2>Register user</h2>

        <?php echo validation_errors(); ?>

        <?php echo form_open('pesa/addUser'); ?>

            <label for="name">Full name    </label>
            <input type="text" name="name" /><br />
            <label for="phone">Phone number</label>
            <input type="text" name="phone" /><br />

            <input type="submit" name="submit" value="Add user" />

        </form>

    </body>
</html> 