<?php if(isset($transactionHistory) && count($transactionHistory) > 0): ?>
    <h4 style="font-weight: bold;">LAST 5 TRANSACTION:</h4>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }

        th {
            background-color: #333;
            color: white;
        }

        .debit {
            color: green;
        }

        .credit {
            color: red;
        }
    </style>
    <table>
        <thead>
            <tr>
                <th>SL</th>
                <th>Transaction Date</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $sl = 1;
            ?>

            <?php $__currentLoopData = $transactionHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($sl); ?></td>
                    <td><?php echo e($transaction->asb_submit_time); ?></td>
                    <td class="debit"><?php echo e(number_format($transaction->asb_debit)); ?></td>
                    <td class="credit"><?php echo e(number_format($transaction->asb_credit)); ?></td>
                    <td><?php echo e($transaction->asb_pay_ref); ?></td>
                </tr>
                <?php
                    $sl++;
                ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php endif; ?>
