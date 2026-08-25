

<?php $__env->startSection('load_menu_class','open'); ?>
<?php $__env->startSection('submenu_load','open'); ?>
<?php $__env->startSection('load_make_check_class','active'); ?>

<?php $__env->startSection('page_location'); ?>
<ul class="breadcrumb">
    <li>
        <i class="ace-icon fa fa-home home-icon"></i>
        <a href="<?php echo e(route('user.index')); ?>">Dashboard</a>
    </li>
    <li class="active">Flexiload Offer Check</li>
</ul>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_header'); ?>
<h1>
    Flexiload
    <small>
        <i class="ace-icon fa fa-angle-double-right"></i>
        Offer Check
    </small>
</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

<div class="row">
    <?php echo $__env->make('user.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <div class="col-xs-12">
        <div class="widget-box widget-color-blue">
            <div class="widget-header">
                <h4 class="widget-title lighter">
                    <i class="ace-icon fa fa-search"></i>
                    Check Flexiload Offer
                </h4>
            </div>

            <div class="widget-body">
                <div class="widget-main">
                    <form action="<?php echo e(url('offer-check')); ?>" method="POST" class="form-horizontal">
                        <?php echo csrf_field(); ?>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="operator">Operator</label>
                            <div class="col-sm-6">
                                <select name="operator" id="operator" class="form-control" required>
                                    <option value="">-- Select Operator --</option>
                                    <option value="grameen" <?php echo e(old('operator') == 'grameen' ? 'selected' : ''); ?>>Grameenphone</option>
                                    <option value="rb" <?php echo e(old('operator') == 'rb' ? 'selected' : ''); ?>>Robi</option>
                                    <option value="at" <?php echo e(old('operator') == 'at' ? 'selected' : ''); ?>>Airtel</option>
                                    <option value="bl" <?php echo e(old('operator') == 'bl' ? 'selected' : ''); ?>>Banglalink</option>
                                    <option value="teletalk" <?php echo e(old('operator') == 'teletalk' ? 'selected' : ''); ?>>Teletalk</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="number">Phone Number</label>
                            <div class="col-sm-6">
                                <input type="text" id="number" name="number" value="<?php echo e(old('number')); ?>" class="form-control phone-input-large" placeholder="e.g. 017xxxxxxxx" required>
                            </div>
                        </div>

                        <input type="hidden" id="amount" name="amount" value="10">   

                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa fa-search"></i> Check Offer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<?php if(isset($apiResponse) || isset($error)): ?>
<?php
    // Handle different response structures for different operators
    $offers = [];
    $responseKeys = [];
    
    if(isset($apiResponse)) {
        $responseKeys = array_keys($apiResponse);
        
        // For Robi - offers are directly in 'offers' array
        if(isset($request) && $request->operator == 'rb' && isset($apiResponse['offers']) && is_array($apiResponse['offers'])) {
            $offers = $apiResponse['offers'];
        }
        elseif(isset($request) && $request->operator == 'at' && isset($apiResponse['offers']) && is_array($apiResponse['offers'])) {
            $offers = $apiResponse['offers'];
        }
        // Banglalink specific response structure
        elseif(isset($request) && $request->operator == 'bl' && isset($apiResponse['offers']) && is_array($apiResponse['offers'])) {
            $offers = $apiResponse['offers'];
        }
        // Other possible response structures
        elseif(isset($apiResponse['offers']) && is_array($apiResponse['offers'])) {
            $offers = $apiResponse['offers'];
        }
        elseif(isset($apiResponse['data']) && is_array($apiResponse['data'])) {
            $offers = $apiResponse['data'];
        }
        elseif(isset($apiResponse['offerList']) && is_array($apiResponse['offerList'])) {
            $offers = $apiResponse['offerList'];
        }
        elseif(isset($apiResponse['list']) && is_array($apiResponse['list'])) {
            $offers = $apiResponse['list'];
        }
        elseif(isset($apiResponse['result']) && is_array($apiResponse['result'])) {
            $offers = $apiResponse['result'];
        }
        elseif(isset($apiResponse['offersList']) && is_array($apiResponse['offersList'])) {
            $offers = $apiResponse['offersList'];
        }
        // If no specific key found, check if the response itself is an array of offers
        elseif(is_array($apiResponse) && count($apiResponse) > 0 && isset($apiResponse[0]['offerId'])) {
            $offers = $apiResponse;
        }
    }
    
    $offersCount = count($offers);
    
    // Filter different package types
    $thirtyDaysOffers = [];
    $sevenDaysOffers = [];
    $dataPackages = [];
    $minutePackages = [];
    $comboPackages = [];
    $socialPackages = [];
    $streamingPackages = [];
    $unlimitedPackages = [];
    
    foreach($offers as $offer) {
        // Handle different field names for Robi vs Banglalink
        if(isset($request) && $request->operator == 'rb') {
            $offerSummary = $offer['subscriberOfferMessage'] ?? $offer['offerSummary'] ?? $offer['offerDescription'] ?? $offer['description'] ?? $offer['name'] ?? '';
            $validity = $offer['validity'] ?? $offer['offerValidity'] ?? $offer['offerValidityHours'] ?? $offer['duration'] ?? '';
        } else {
            $offerSummary = $offer['offerSummary'] ?? $offer['offerDescription'] ?? $offer['description'] ?? $offer['name'] ?? $offer['subscriberOfferMessage'] ?? '';
            $validity = $offer['validity'] ?? $offer['offerValidity'] ?? $offer['offerValidityHours'] ?? $offer['duration'] ?? '';
        }
        
        // Check package types based on summary and validity
        $summaryLower = strtolower($offerSummary);
        
        // 30 Days offers
        if (stripos($offerSummary, '30 day') !== false || 
            stripos($offerSummary, '30 days') !== false ||
            stripos($offerSummary, '30d') !== false ||
            stripos($validity, '30') !== false) {
            $thirtyDaysOffers[] = $offer;
        }
        
        // 7 Days offers
        if (stripos($offerSummary, '7 day') !== false || 
            stripos($offerSummary, '7 days') !== false ||
            stripos($offerSummary, '7d') !== false ||
            stripos($validity, '7') !== false) {
            $sevenDaysOffers[] = $offer;
        }
        
        // Data packages (contains GB, data, internet)
        if (stripos($summaryLower, 'gb') !== false || 
            stripos($summaryLower, 'data') !== false ||
            stripos($summaryLower, 'internet') !== false) {
            $dataPackages[] = $offer;
        }
        
        // Minute packages (contains min, minute, call, voice)
        if (stripos($summaryLower, 'min') !== false || 
            stripos($summaryLower, 'minute') !== false ||
            stripos($summaryLower, 'call') !== false ||
            stripos($summaryLower, 'voice') !== false) {
            $minutePackages[] = $offer;
        }
        
        // Combo packages (contains both data and minutes, or "combo")
        if ((stripos($summaryLower, 'gb') !== false && stripos($summaryLower, 'min') !== false) ||
            stripos($summaryLower, 'combo') !== false ||
            stripos($summaryLower, 'both') !== false) {
            $comboPackages[] = $offer;
        }
        
        // Social packages (contains social media names)
        if (stripos($summaryLower, 'facebook') !== false || 
            stripos($summaryLower, 'whatsapp') !== false ||
            stripos($summaryLower, 'messenger') !== false ||
            stripos($summaryLower, 'instagram') !== false ||
            stripos($summaryLower, 'imo') !== false ||
            stripos($summaryLower, 'viber') !== false ||
            stripos($summaryLower, 'social') !== false) {
            $socialPackages[] = $offer;
        }
        
        // Streaming packages (contains streaming platform names)
        if (stripos($summaryLower, 'youtube') !== false || 
            stripos($summaryLower, 'tiktok') !== false ||
            stripos($summaryLower, 'snapchat') !== false ||
            stripos($summaryLower, 'likee') !== false ||
            stripos($summaryLower, 'streaming') !== false ||
            stripos($summaryLower, 'video') !== false) {
            $streamingPackages[] = $offer;
        }
        
        // Unlimited packages
        if (stripos($summaryLower, 'unlimited') !== false) {
            $unlimitedPackages[] = $offer;
        }
    }
    
    // Counts for each category
    $thirtyDaysCount = count($thirtyDaysOffers);
    $sevenDaysCount = count($sevenDaysOffers);
    $dataCount = count($dataPackages);
    $minuteCount = count($minutePackages);
    $comboCount = count($comboPackages);
    $socialCount = count($socialPackages);
    $streamingCount = count($streamingPackages);
    $unlimitedCount = count($unlimitedPackages);
?>

<div class="row mt-4">
    <div class="col-xs-12">
        <div class="widget-box widget-color-green2">
            <div class="widget-header">
                <h4 class="widget-title lighter">
                    <i class="ace-icon fa fa-gift"></i>
                    Available Offers
                </h4>
            </div>
            <div class="widget-body">
                <div class="widget-main">
                    <?php if(isset($request) && $request->has('number') && $request->has('operator') && $request->has('amount')): ?>
                    <div class="search-info mb-4 p-4 bg-light rounded">
                        <div class="row text-center">
                            <div class="col-md-4 col-sm-4 col-xs-4">
                                <small><strong>Operator:</strong></small><br>
                                <span class="badge operator-badge-lg" id="operator-badge">
                                    <?php if($request->operator == 'grameen'): ?> GP
                                    <?php elseif($request->operator == 'rb'): ?> Robi
                                    <?php elseif($request->operator == 'at'): ?> Airtel
                                    <?php elseif($request->operator == 'bl'): ?> BL
                                    <?php elseif($request->operator == 'teletalk'): ?> TT
                                    <?php else: ?> <?php echo e(ucfirst($request->operator)); ?>

                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-4">
                                <small><strong>Phone Number:</strong></small><br>
                                <span class="phone-number-large"><?php echo e($request->number); ?></span>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-4">
                                <small><strong>Total Offers:</strong></small><br>
                                <span class="badge badge-info"><?php echo e($offersCount); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger text-center py-3">
                            <i class="fa fa-exclamation-triangle"></i> <?php echo e($error); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($offersCount > 0): ?>
                        <!-- Enhanced Filter Buttons with Multi-select AND Logic -->
                        <div class="filter-buttons mb-4">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                                        <button type="button" class="btn btn-primary btn-filter-multi active" data-filter="all">
                                            <i class="fa fa-list"></i> All Offers (<?php echo e($offersCount); ?>)
                                        </button>
                                        <button type="button" class="btn btn-success btn-filter-multi" data-filter="30days">
                                            <i class="fa fa-calendar"></i> 30 Days (<?php echo e($thirtyDaysCount); ?>)
                                        </button>
                                        <button type="button" class="btn btn-warning btn-filter-multi" data-filter="7days">
                                            <i class="fa fa-calendar"></i> 7 Days (<?php echo e($sevenDaysCount); ?>)
                                        </button>
                                        <button type="button" class="btn btn-info btn-filter-multi" data-filter="data">
                                            <i class="fa fa-database"></i> Data (<?php echo e($dataCount); ?>)
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-filter-multi" data-filter="minutes">
                                            <i class="fa fa-phone"></i> Minutes (<?php echo e($minuteCount); ?>)
                                        </button>
                                        <button type="button" class="btn btn-purple btn-filter-multi" data-filter="combo">
                                            <i class="fa fa-layer-group"></i> Combo (<?php echo e($comboCount); ?>)
                                        </button>
                                        <button type="button" class="btn btn-pink btn-filter-multi" data-filter="social">
                                            <i class="fa fa-share-alt"></i> Social (<?php echo e($socialCount); ?>)
                                        </button>
                                        <button type="button" class="btn btn-orange btn-filter-multi" data-filter="streaming">
                                            <i class="fa fa-video"></i> Streaming (<?php echo e($streamingCount); ?>)
                                        </button>
                                        <button type="button" class="btn btn-dark btn-filter-multi" data-filter="unlimited">
                                            <i class="fa fa-infinity"></i> Unlimited (<?php echo e($unlimitedCount); ?>)
                                        </button>
                                    </div>
                                    
                                    <!-- Selected Filters Display -->
                                    <div class="selected-filters-container mb-3 text-center" style="display: none;">
                                        <div class="d-inline-block">
                                            <span class="badge bg-light text-dark me-2">
                                                Active Filters (AND Logic): 
                                                <span id="selected-filters-list"></span>
                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="clear-filters">
                                                <i class="fa fa-times"></i> Clear All
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Filter Status Message -->
                                    <div class="alert alert-info text-center py-2 mb-3" id="filter-status">
                                        <i class="fa fa-filter"></i> Showing all <?php echo e($offersCount); ?> offers
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- All Offers Grid with Multi-filter AND Support -->
                        <div class="offers-grid-8-col">
                            <div class="offers-container-8" id="offers-container">
                                <?php $__currentLoopData = $offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        // Handle different field names for different operators
                                        $offerId = '';
                                        $offerAmount = '';
                                        $offerSummary = '';
                                        $offerValidity = '';
                                        $offerUssd = '';
                                        $offerType = '';
                                        $commissionAmount = 0;
                                        $retailerMessage = '';
                                        $isHighlighted = false;
                                        
                                        // Banglalink specific field mapping
                                        if($request->operator == 'bl') {
                                            $offerId = $offer['offerId'] ?? $offer['id'] ?? $offer['offer_id'] ?? $offer['packageId'] ?? $offer['package_id'] ?? '';
                                            $offerAmount = $offer['amount'] ?? $offer['price'] ?? $offer['offerPrice'] ?? $offer['offer_price'] ?? $offer['packagePrice'] ?? $offer['package_price'] ?? '0';
                                            $offerSummary = $offer['offerSummary'] ?? $offer['summary'] ?? $offer['offerDescription'] ?? $offer['description'] ?? $offer['name'] ?? $offer['packageName'] ?? $offer['package_name'] ?? 'Banglalink Offer';
                                            $offerValidity = $offer['validity'] ?? $offer['offerValidity'] ?? $offer['validityHours'] ?? $offer['offerValidityHours'] ?? $offer['duration'] ?? $offer['validity_days'] ?? 'N/A';
                                            $offerUssd = $offer['ussdCode'] ?? $offer['ussd'] ?? $offer['optInKeyword'] ?? $offer['code'] ?? $offer['ussdString'] ?? $offer['ussd_code'] ?? '';
                                            $offerType = $offer['offerType'] ?? $offer['type'] ?? $offer['offerContentType'] ?? $offer['category'] ?? $offer['offer_type'] ?? 'Offer';
                                            $commissionAmount = $offer['commission'] ?? $offer['commissionAmount'] ?? 0;
                                        } 
                                        // Robi specific field mapping
                                        elseif($request->operator == 'rb') {
                                            $offerId = $offer['ticketId'] ?? $offer['offerId'] ?? '';
                                            $offerAmount = (float) ($offer['amount'] ?? 0);
                                            $offerSummary = $offer['subscriberOfferMessage'] ?? $offer['offerSummary'] ?? 'Robi Offer';
                                            $offerValidity = $offer['validity'] ?? 'N/A';
                                            $offerUssd = $offer['ussdCode'] ?? '';
                                            $offerType = $offer['offerType'] ?? 'Offer';
                                            $commissionAmount = (float) ($offer['commissionAmount'] ?? 0);
                                            $retailerMessage = $offer['retailerOfferMessage'] ?? '';
                                            $isHighlighted = (bool) ($offer['isHighlighted'] ?? false);
                                        } 
                                         elseif($request->operator == 'at') {
                                            $offerId = $offer['ticketId'] ?? $offer['offerId'] ?? '';
                                            $offerAmount = (float) ($offer['amount'] ?? 0);
                                            $offerSummary = $offer['subscriberOfferMessage'] ?? $offer['offerSummary'] ?? 'Airtel Offer';
                                            $offerValidity = $offer['validity'] ?? 'N/A';
                                            $offerUssd = $offer['ussdCode'] ?? '';
                                            $offerType = $offer['offerType'] ?? 'Offer';
                                            $commissionAmount = (float) ($offer['commissionAmount'] ?? 0);
                                            $retailerMessage = $offer['retailerOfferMessage'] ?? '';
                                            $isHighlighted = (bool) ($offer['isHighlighted'] ?? false);
                                        } 
                                        // Grameenphone specific field mapping
                                        elseif($request->operator == 'grameen') {
                                            $offerId = $offer['offerId'] ?? '';
                                            $offerAmount = $offer['offerPrice'] ?? '0';
                                            $offerSummary = $offer['offerDescription'] ?? 'N/A';
                                            $offerValidity = ($offer['offerValidityHours'] ?? 'N/A') . 'H';
                                            $offerUssd = $offer['optInKeyword'] ?? '';
                                            $offerType = $offer['offerContentType'] ?? '';
                                        }
                                        // Other operators
                                        else {
                                            $offerId = $offer['offerId'] ?? '';
                                            $offerAmount = $offer['amount'] ?? '0';
                                            $offerSummary = $offer['offerSummary'] ?? 'N/A';
                                            $offerValidity = $offer['validity'] ?? 'N/A';
                                            $offerUssd = $offer['ussdCode'] ?? '';
                                            $offerType = $offer['offerType'] ?? '';
                                        }
                                        
                                        // Clean summary - remove "C-..." patterns and trim
                                        $cleanSummary = preg_replace('/C-[^\s]*/', '', $offerSummary);
                                        $cleanSummary = trim($cleanSummary);
                                        if(empty($cleanSummary)) {
                                            $cleanSummary = 'Special Offer';
                                        }
                                        
                                        // Extract first part if separated by |
                                        if(strpos($cleanSummary, '|') !== false) {
                                            $cleanSummary = explode('|', $cleanSummary)[0];
                                        }
                                        
                                        // Ensure amount is numeric
                                        if(!is_numeric($offerAmount)) {
                                            $offerAmount = 0;
                                        }
                                        
                                        // For Robi offers, extract validity from the message if not available
                                        if($request->operator == 'rb' && ($offerValidity == 'N/A' || empty($offerValidity))) {
                                            // Try to extract validity from subscriber message
                                            if (preg_match('/(\d+)\s*(Days?|Hours?)/i', $offerSummary, $matches)) {
                                                $number = $matches[1];
                                                $unit = strtolower($matches[2]);
                                                if (strpos($unit, 'day') !== false) {
                                                    $offerValidity = $number . ' Day' . ($number > 1 ? 's' : '');
                                                } elseif (strpos($unit, 'hour') !== false) {
                                                    $offerValidity = $number . ' Hour' . ($number > 1 ? 's' : '');
                                                }
                                            }
                                        }
                                          if($request->operator == 'at' && ($offerValidity == 'N/A' || empty($offerValidity))) {
                                            // Try to extract validity from subscriber message
                                            if (preg_match('/(\d+)\s*(Days?|Hours?)/i', $offerSummary, $matches)) {
                                                $number = $matches[1];
                                                $unit = strtolower($matches[2]);
                                                if (strpos($unit, 'day') !== false) {
                                                    $offerValidity = $number . ' Day' . ($number > 1 ? 's' : '');
                                                } elseif (strpos($unit, 'hour') !== false) {
                                                    $offerValidity = $number . ' Hour' . ($number > 1 ? 's' : '');
                                                }
                                            }
                                        }
                                        
                                        // Determine package type classes with AND logic support
                                        $packageTypes = [];
                                        $summaryLower = strtolower($cleanSummary);
                                        
                                        // Duration filters
                                        $is30Days = false;
                                        $is7Days = false;
                                        if (stripos($cleanSummary, '30 day') !== false || stripos($cleanSummary, '30 days') !== false || stripos($offerValidity, '30') !== false) {
                                            $packageTypes[] = 'filter-30days';
                                            $is30Days = true;
                                        }
                                        if (stripos($cleanSummary, '7 day') !== false || stripos($cleanSummary, '7 days') !== false || stripos($offerValidity, '7') !== false) {
                                            $packageTypes[] = 'filter-7days';
                                            $is7Days = true;
                                        }
                                        
                                        // Content type filters
                                        $hasData = false;
                                        $hasMinutes = false;
                                        $hasCombo = false;
                                        $hasSocial = false;
                                        $hasStreaming = false;
                                        $hasUnlimited = false;
                                        
                                        if (stripos($summaryLower, 'gb') !== false || stripos($summaryLower, 'data') !== false || stripos($summaryLower, 'internet') !== false) {
                                            $packageTypes[] = 'filter-data';
                                            $hasData = true;
                                        }
                                        if (stripos($summaryLower, 'min') !== false || stripos($summaryLower, 'minute') !== false || stripos($summaryLower, 'call') !== false) {
                                            $packageTypes[] = 'filter-minutes';
                                            $hasMinutes = true;
                                        }
                                        if ((stripos($summaryLower, 'gb') !== false && stripos($summaryLower, 'min') !== false) || stripos($summaryLower, 'combo') !== false) {
                                            $packageTypes[] = 'filter-combo';
                                            $hasCombo = true;
                                        }
                                        if (stripos($summaryLower, 'facebook') !== false || stripos($summaryLower, 'whatsapp') !== false || stripos($summaryLower, 'social') !== false) {
                                            $packageTypes[] = 'filter-social';
                                            $hasSocial = true;
                                        }
                                        if (stripos($summaryLower, 'youtube') !== false || stripos($summaryLower, 'tiktok') !== false || stripos($summaryLower, 'streaming') !== false) {
                                            $packageTypes[] = 'filter-streaming';
                                            $hasStreaming = true;
                                        }
                                        if (stripos($summaryLower, 'unlimited') !== false) {
                                            $packageTypes[] = 'filter-unlimited';
                                            $hasUnlimited = true;
                                        }
                                        
                                        // For combo packages, also mark as data and minutes
                                        if ($hasCombo) {
                                            $packageTypes[] = 'filter-data';
                                            $packageTypes[] = 'filter-minutes';
                                        }
                                        
                                        $packageTypesString = implode(' ', array_unique($packageTypes));
                                        $dataFilterTypes = implode(',', array_unique($packageTypes));
                                    ?>
                                    
                                    <div class="offer-card-8-col offer-item <?php echo e($packageTypesString); ?>" 
                                         data-filter-types="<?php echo e($dataFilterTypes); ?>"
                                         data-30days="<?php echo e($is30Days ? 'yes' : 'no'); ?>"
                                         data-7days="<?php echo e($is7Days ? 'yes' : 'no'); ?>"
                                         data-data="<?php echo e($hasData ? 'yes' : 'no'); ?>"
                                         data-minutes="<?php echo e($hasMinutes ? 'yes' : 'no'); ?>"
                                         data-combo="<?php echo e($hasCombo ? 'yes' : 'no'); ?>"
                                         data-social="<?php echo e($hasSocial ? 'yes' : 'no'); ?>"
                                         data-streaming="<?php echo e($hasStreaming ? 'yes' : 'no'); ?>"
                                         data-unlimited="<?php echo e($hasUnlimited ? 'yes' : 'no'); ?>">
                                        <div class="offer-header-8-col">
                                            <div class="operator-logo-8-col">
                                                <?php if(isset($request)): ?>
                                                    <?php switch($request->operator):
                                                        case ('grameen'): ?>
                                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/Grameenphone_Logo_GP_Logo.svg/180px-Grameenphone_Logo_GP_Logo.svg.png" alt="GP" class="operator-logo-img">
                                                            <?php break; ?>
                                                        <?php case ('rb'): ?>
                                                            <img src="https://upload.wikimedia.org/wikipedia/en/thumb/7/7b/Logo_of_Robi_Axiata.svg/225px-Logo_of_Robi_Axiata.svg.png" alt="Robi" class="operator-logo-img">
                                                            <?php break; ?>
                                                        <?php case ('at'): ?>
                                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Bharti_Airtel_Logo.svg/330px-Bharti_Airtel_Logo.svg.png" alt="Airtel" class="operator-logo-img">
                                                            <?php break; ?>
                                                        <?php case ('bl'): ?>
                                             <img 
  src="https://banglalink.net/logo.svg" 
  alt="Banglalink" 
  class="operator-logo-img"
  style="width: 80px; height: 40px;"
>

                                                            <?php break; ?>
                                                        <?php case ('teletalk'): ?>
                                                            <div class="operator-logo-placeholder-8 teletalk-logo-8">
                                                                <i class="fas fa-satellite"></i>
                                                            </div>
                                                            <?php break; ?>
                                                        <?php default: ?>
                                                            <div class="operator-logo-placeholder-8">
                                                                <i class="fas fa-sim-card"></i>
                                                            </div>
                                                    <?php endswitch; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="offer-content-8-col">
                                            <h6 class="offer-summary-8-col" title="<?php echo e($cleanSummary); ?>">
                                                <?php echo e($cleanSummary); ?>

                                                <?php if($isHighlighted): ?>
                                                    <span class="badge-highlight">🔥</span>
                                                <?php endif; ?>
                                            </h6>
                                        </div>

                                        <div class="offer-price-8-col">
                                            <div class="price-display-8-col">
                                                <span class="price-amount-8-col"><?php echo e($offerAmount); ?></span>
                                                <span class="price-currency-8-col">Tk</span>
                                            </div>
                                        </div>

                                        <div class="offer-details-8-col">
                                            <div class="detail-item-8-col">
                                                <i class="fas fa-clock"></i>
                                                <?php
                                                    // Keep original unchanged
                                                    $displayValidity = $offerValidity;

                                                    // Try to extract number (handles "24H", "72 Hours", "48 hrs", etc.)
                                                    if (preg_match('/\d+/', $offerValidity, $matches)) {
                                                        $hours = (int) $matches[0];

                                                        // Check if the original text looks like hours
                                                        if (stripos($offerValidity, 'hour') !== false || stripos($offerValidity, 'hr') !== false || stripos($offerValidity, 'h') !== false) {
                                                            $days = intdiv($hours, 24);
                                                            $remainingHours = $hours % 24;

                                                            if ($days > 0 && $remainingHours > 0) {
                                                                $displayValidity = "{$days} Day" . ($days > 1 ? 's' : '') . " {$remainingHours} Hour" . ($remainingHours > 1 ? 's' : '');
                                                            } elseif ($days > 0) {
                                                                $displayValidity = "{$days} Day" . ($days > 1 ? 's' : '');
                                                            } else {
                                                                $displayValidity = "{$remainingHours} Hour" . ($remainingHours > 1 ? 's' : '');
                                                            }
                                                        }
                                                    }
                                                ?>
                                                <span class="validity-8-col" style="font-size:22px; font-weight:600; color:#2c3e50;">
                                                    <?php echo e($displayValidity); ?>

                                                </span>
                                            </div>
                                        </div>

                                        <div class="offer-footer-8-col">
                                            <button type="button" 
                                                    class="btn-buy-now-8-col" 
                                                    data-offer-id="<?php echo e($offerId); ?>"
                                                    data-amount="<?php echo e($offerAmount); ?>"
                                                    data-ussd="<?php echo e($offerUssd); ?>"
                                                    data-summary="<?php echo e($cleanSummary); ?>"
                                                    data-validity="<?php echo e($offerValidity); ?>"
                                                    data-offer-type="<?php echo e($offerType); ?>"
                                                    data-number="<?php echo e($request->number ?? ''); ?>"
                                                    data-operator="<?php echo e($request->operator ?? ''); ?>">
                                                <i class="fas fa-shopping-cart"></i> Buy Now
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                    <?php elseif(isset($apiResponse)): ?>
                        <div class="alert alert-info text-center py-3">
                            <i class="fa fa-info-circle"></i> 
                            <?php if(isset($apiResponse['message'])): ?>
                                <?php echo e($apiResponse['message']); ?>

                            <?php else: ?>
                                No offers found for the specified criteria.
                                <?php if(env('APP_DEBUG')): ?>
                                    <br><small>Available response keys: <?php echo e(implode(', ', $responseKeys)); ?></small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Buy Offer Modal -->
<div class="modal fade" id="buyOfferModal" tabindex="-1" role="dialog" aria-labelledby="offerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="offerModalLabel">Purchase Offer</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo e(url('offer-buy')); ?>" method="post" id="offerPurchaseForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading mb-3" id="modal_offer_summary"></h5>
                        <div class="row text-center">
                            <div class="col-md-3 col-6 mb-3">
                                <div class="modal-info-item">
                                    <div class="modal-info-label">Price</div>
                                    <div class="modal-info-value price-highlight-lg" id="modal_offer_amount"></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="modal-info-item">
                                    <div class="modal-info-label">Validity</div>
                                    <div class="modal-info-value" id="modal_offer_validity"></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="modal-info-item">
                                    <div class="modal-info-label">Type</div>
                                    <div class="modal-info-value" id="modal_offer_type"></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="modal-info-item">
                                    <div class="modal-info-label">USSD Code</div>
                                    <div class="modal-info-value ussd-code-lg" id="modal_offer_ussd"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" name="offer_id" id="modal_offer_id">
                    <input type="hidden" name="offer_amount" id="modal_hidden_amount">
                    <input type="hidden" name="offer_summary" id="modal_hidden_summary">
                    <input type="hidden" name="operator" id="modal_operator">

                    <!-- Form Fields -->
                    <div class="form-group">
    <label for="modal_targeted_number" class="form-label-lg">Mobile Number</label>
    <input type="text" 
           class="form-control form-control-xl phone-input-xl" 
           id="modal_targeted_number" 
           name="targeted_number" 
           placeholder="Enter 11-digit phone number" 
           required
           readonly>
</div>


                    <div class="form-group">
                        <label class="form-label-lg">Number Type</label>
                        <div class="number-type-options-lg">
                            <label class="radio-option-lg">
                                <input type="radio" name="number_type" value="1" checked required>
                                <span class="radio-label-lg">Prepaid</span>
                            </label>
                            <label class="radio-option-lg">
                                <input type="radio" name="number_type" value="2" required>
                                <span class="radio-label-lg">Postpaid</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="modal_campaign_name" class="form-label-lg">Campaign Name (Optional)</label>
                        <input type="text" class="form-control" id="modal_campaign_name" name="campaign_name" placeholder="Enter campaign name">
                    </div>

                    <div class="form-group">
                        <label for="modal_owner_name" class="form-label-lg">Number Owner Name (Optional)</label>
                        <input type="text" class="form-control" id="modal_owner_name" name="owner_name" placeholder="Enter owner name">
                    </div>

                    <div class="form-group">
                        <label for="modal_remarks" class="form-label-lg">Remarks (Optional)</label>
                        <input type="text" class="form-control" id="modal_remarks" name="remarks" placeholder="Enter remarks">
                    </div>

                    <div class="form-group">
                        <label for="modal_flexipin" class="form-label-lg">Flexipin</label>
                        <input type="password" class="form-control form-control-lg" id="modal_flexipin" name="flexipin" placeholder="Enter your flexipin" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fa fa-shopping-cart"></i> Confirm Purchase
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-purple { background-color: #6f42c1; color: white; border-color: #6f42c1; }
.btn-pink { background-color: #e83e8c; color: white; border-color: #e83e8c; }
.btn-orange { background-color: #fd7e14; color: white; border-color: #fd7e14; }
.btn-group .btn { margin: 2px; }
.badge-highlight { color: #ff6b00; font-size: 12px; }
.commission-badge { background: #28a745; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px; margin-top: 5px; display: inline-block; }

/* Enhanced Filter Styles */
.btn-filter-multi {
    border-radius: 20px;
    padding: 8px 16px;
    margin: 2px;
    transition: all 0.3s ease;
    border-width: 2px;
    font-weight: 600;
    font-size: 14px;
    min-width: 90px;
    text-align: center;
}

.btn-filter-multi.active {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-width: 2px;
    font-weight: 700;
}

.btn-filter-multi:hover:not(.active) {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Selected filters display */
.selected-filters-container {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 10px 15px;
    border: 1px solid #e9ecef;
}

#selected-filters-list .badge {
    font-size: 12px;
    padding: 5px 8px;
    margin: 2px;
}

/* Filter status message */
#filter-status {
    font-size: 14px;
    padding: 8px 15px;
    border-radius: 8px;
    background: #e7f3ff;
    border-left: 4px solid #0069d9;
}

/* 8 Cards Per Row Grid Layout */
.offers-grid-8-col {
    max-height: 700px;
    overflow-y: auto;
    padding-right: 15px;
    margin: 0 -5px;
}

.offers-container-8 {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 12px;
    padding: 10px 5px;
}

/* 8 Column Card Styles - FIXED FOR MOBILE VIEW */
.offer-card-8-col {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border: 1px solid #e8e8e8;
    transition: all 0.3s ease;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: 240px;
    min-width: 0;
    position: relative;
}

.offer-card-8-col:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.offer-header-8-col {
    padding: 8px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #e8e8e8;
    text-align: center;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.operator-logo-8-col {
    display: flex;
    align-items: center;
    justify-content: center;
}

.operator-logo-img {
    width: 28px;
    height: 28px;
    object-fit: contain;
    border-radius: 6px;
}

.operator-logo-placeholder-8 {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.operator-logo-placeholder-8.teletalk-logo-8 {
    background: #0066b3;
}

.offer-content-8-col {
    padding: 8px;
    flex: 1;
    display: flex;
    align-items: center;
    min-height: 45px;
    max-height: 50px;
    overflow: hidden;
}

.offer-summary-8-col {
    font-size: 13px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    line-height: 1.3;
    text-align: center;
    width: 100%;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.offer-price-8-col {
    padding: 5px 8px;
    text-align: center;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.price-display-8-col {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    color: white;
    padding: 5px 8px;
    border-radius: 8px;
    display: inline-flex;
    align-items: baseline;
    gap: 4px;
    box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);
}

.price-amount-8-col {
    font-size: 14px;
    font-weight: 800;
}

.price-currency-8-col {
    font-size: 10px;
    font-weight: 700;
}

.offer-details-8-col {
    padding: 5px 8px;
    border-top: 1px dashed #e8e8e8;
    min-height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.detail-item-8-col {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    font-size: 9px;
    color: #6c757d;
}

.validity-8-col {
    font-size: 18px;
    font-weight: 700;
    color: #e67e22;
}

/* BUY NOW BUTTON - ALWAYS VISIBLE */
.offer-footer-8-col {
    padding: 6px 8px;
    background: #f8f9fa;
    border-top: 1px solid #e8e8e8;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    position: relative;
    z-index: 10;
    margin-top: auto;
}

.btn-buy-now-8-col {
    width: 100%;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border: none;
    padding: 6px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
    min-height: 30px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    position: relative;
    z-index: 20;
}

.btn-buy-now-8-col:hover {
    background: linear-gradient(135deg, #218838, #1ea085);
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(40, 167, 69, 0.4);
}

/* Enhanced Phone Number Styles */
.phone-input-large {
    font-size: 16px;
    font-weight: 600;
    padding: 10px 12px;
    height: 46px;
}

.phone-input-xl {
    font-size: 18px;
    font-weight: 700;
    padding: 12px 15px;
    height: 52px;
    letter-spacing: 1px;
}

.phone-number-large {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
}

.amount-large {
    font-size: 18px;
    font-weight: 700;
    color: #27ae60;
}

.operator-badge-lg {
    font-size: 14px;
    padding: 8px 16px;
}

/* Enhanced Modal Styles */
.form-label-lg {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
}

.price-highlight-lg {
    color: #28a745;
    font-size: 18px;
    font-weight: 800;
}

.ussd-code-lg {
    font-family: 'Courier New', monospace;
    background: #f8f9fa;
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    font-size: 14px;
    font-weight: 600;
}

.number-type-options-lg {
    display: flex;
    gap: 25px;
}

.radio-option-lg {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-size: 15px;
}

.radio-label-lg {
    font-weight: 600;
    color: #495057;
    font-size: 15px;
}

/* Scrollbar styling */
.offers-grid-8-col::-webkit-scrollbar {
    width: 8px;
}

.offers-grid-8-col::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 6px;
}

.offers-grid-8-col::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 6px;
}

.offers-grid-8-col::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Responsive adjustments for 8-column layout - MOBILE FIXES */
@media (max-width: 1600px) {
    .offers-container-8 {
        grid-template-columns: repeat(6, 1fr);
    }
}

@media (max-width: 1200px) {
    .offers-container-8 {
        grid-template-columns: repeat(5, 1fr);
    }
}

@media (max-width: 992px) {
    .offers-container-8 {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .btn-filter-multi {
        padding: 6px 12px;
        font-size: 12px;
        min-width: 80px;
        margin: 1px;
    }
    
    #filter-status {
        font-size: 13px;
        padding: 6px 10px;
    }
}

@media (max-width: 768px) {
    .offers-container-8 {
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }
    
    .offer-card-8-col {
        min-height: 220px;
    }
    
    .offer-header-8-col {
        min-height: 35px;
        padding: 6px;
    }
    
    .offer-content-8-col {
        padding: 6px;
        min-height: 40px;
        max-height: 45px;
    }
    
    .offer-summary-8-col {
        font-size: 12px;
    }
    
    .offer-price-8-col {
        min-height: 35px;
        padding: 4px 6px;
    }
    
    .price-display-8-col {
        padding: 4px 6px;
    }
    
    .price-amount-8-col {
        font-size: 13px;
    }
    
    .price-currency-8-col {
        font-size: 9px;
    }
    
    .offer-details-8-col {
        padding: 4px 6px;
        min-height: 22px;
    }
    
    .detail-item-8-col {
        font-size: 8px;
        gap: 3px;
    }
    
    .validity-8-col {
        font-size: 16px;
    }
    
    .offer-footer-8-col {
        padding: 4px 6px;
        min-height: 35px;
    }
    
    .btn-buy-now-8-col {
        padding: 5px 6px;
        font-size: 10px;
        min-height: 28px;
    }
}

@media (max-width: 576px) {
    .offers-container-8 {
        grid-template-columns: repeat(2, 1fr);
        gap: 6px;
    }
    
    .offer-card-8-col {
        min-height: 200px;
    }
    
    .btn-filter-multi {
        padding: 5px 10px;
        font-size: 11px;
        min-width: 70px;
    }
    
    .offer-header-8-col {
        min-height: 30px;
        padding: 4px;
    }
    
    .operator-logo-img {
        width: 24px;
        height: 24px;
    }
    
    .offer-content-8-col {
        padding: 4px;
        min-height: 35px;
        max-height: 40px;
    }
    
    .offer-summary-8-col {
        font-size: 11px;
        line-height: 1.2;
    }
    
    .offer-price-8-col {
        min-height: 30px;
        padding: 3px 4px;
    }
    
    .price-display-8-col {
        padding: 3px 5px;
    }
    
    .price-amount-8-col {
        font-size: 12px;
    }
    
    .offer-details-8-col {
        padding: 3px 4px;
        min-height: 20px;
    }
    
    .validity-8-col {
        font-size: 14px;
    }
    
    .offer-footer-8-col {
        padding: 3px 4px;
        min-height: 30px;
    }
    
    .btn-buy-now-8-col {
        padding: 4px 5px;
        font-size: 9px;
        min-height: 26px;
        gap: 3px;
    }
    
    .phone-number-large {
        font-size: 16px;
    }
    
    .amount-large {
        font-size: 16px;
    }
}

/* Extra small devices */
@media (max-width: 400px) {
    .offers-container-8 {
        grid-template-columns: 1fr;
        gap: 5px;
    }
    
    .offer-card-8-col {
        min-height: 190px;
        max-width: 250px;
        margin: 0 auto;
    }
    
    .btn-buy-now-8-col {
        font-size: 10px;
        padding: 5px;
    }
}

/* Animation for cards */
@keyframes  fadeInUp {
    from { 
        opacity: 0; 
        transform: translateY(20px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

.offer-card-8-col {
    animation: fadeInUp 0.5s ease forwards;
}

/* Stagger animation for multiple cards */
.offer-card-8-col:nth-child(1) { animation-delay: 0.1s; }
.offer-card-8-col:nth-child(2) { animation-delay: 0.15s; }
.offer-card-8-col:nth-child(3) { animation-delay: 0.2s; }
.offer-card-8-col:nth-child(4) { animation-delay: 0.25s; }
.offer-card-8-col:nth-child(5) { animation-delay: 0.3s; }
.offer-card-8-col:nth-child(6) { animation-delay: 0.35s; }
.offer-card-8-col:nth-child(7) { animation-delay: 0.4s; }
.offer-card-8-col:nth-child(8) { animation-delay: 0.45s; }
</style>
<script>
// Enhanced Multi-filter functionality with AND logic
document.addEventListener('DOMContentLoaded', function() {
    let activeFilters = new Set(['all']); // Start with 'all' selected
    const allOfferCards = document.querySelectorAll('.offer-item');
    const filterButtons = document.querySelectorAll('.btn-filter-multi');
    const clearFiltersBtn = document.getElementById('clear-filters');
    const selectedFiltersContainer = document.querySelector('.selected-filters-container');
    const selectedFiltersList = document.getElementById('selected-filters-list');
    const filterStatus = document.getElementById('filter-status');
    
    // Filter names mapping for display
    const filterNames = {
        'all': 'All',
        '30days': '30 Days',
        '7days': '7 Days',
        'data': 'Data',
        'minutes': 'Minutes',
        'combo': 'Combo',
        'social': 'Social',
        'streaming': 'Streaming',
        'unlimited': 'Unlimited'
    };
    
    // Update UI functions
    function updateSelectedFiltersDisplay() {
        const selected = Array.from(activeFilters);
        
        // Remove 'all' from display if other filters are selected
        const displayFilters = selected.filter(f => f !== 'all');
        
        if (displayFilters.length > 0) {
            selectedFiltersList.innerHTML = displayFilters.map(f => 
                `<span class="badge bg-primary me-1">${filterNames[f]}</span>`
            ).join('');
            selectedFiltersContainer.style.display = 'block';
        } else {
            selectedFiltersContainer.style.display = 'none';
        }
    }
    
    function updateFilterStatus(visibleCount) {
        const selected = Array.from(activeFilters).filter(f => f !== 'all');
        
        if (selected.length === 0) {
            filterStatus.innerHTML = `<i class="fa fa-filter"></i> Showing all ${allOfferCards.length} offers`;
        } else {
            const filterNamesList = selected.map(f => filterNames[f]).join(' + ');
            filterStatus.innerHTML = `<i class="fa fa-filter"></i> Showing ${visibleCount} offers matching ALL: ${filterNamesList}`;
        }
    }
    
    function updateFilterButtons() {
        filterButtons.forEach(button => {
            const filter = button.getAttribute('data-filter');
            if (activeFilters.has(filter)) {
                button.classList.add('active');
            } else {
                button.classList.remove('active');
            }
        });
    }
    
    // Filter logic function with AND logic
    function applyFilters() {
        let visibleCount = 0;
        const selectedFilters = Array.from(activeFilters).filter(f => f !== 'all');
        
        allOfferCards.forEach(card => {
            let shouldShow = false;
            
            // If 'all' is selected or no specific filters are active, show all cards
            if (activeFilters.has('all') || selectedFilters.length === 0) {
                shouldShow = true;
            } else {
                // AND logic: Check if card matches ALL selected filters
                shouldShow = true;
                for (let filter of selectedFilters) {
                    if (card.getAttribute(`data-${filter}`) !== 'yes') {
                        shouldShow = false;
                        break;
                    }
                }
                
                // SPECIAL LOGIC: If both "data" and "minutes" are selected, we need special handling
                if (selectedFilters.includes('data') && selectedFilters.includes('minutes')) {
                    // Show ONLY combo packages when both data and minutes are selected
                    if (card.getAttribute('data-combo') !== 'yes') {
                        shouldShow = false;
                    }
                } else if (selectedFilters.includes('data') && !selectedFilters.includes('minutes')) {
                    // When only "data" is selected, exclude combo packages (show data-only)
                    if (card.getAttribute('data-combo') === 'yes') {
                        shouldShow = false;
                    }
                } else if (selectedFilters.includes('minutes') && !selectedFilters.includes('data')) {
                    // When only "minutes" is selected, exclude combo packages (show minutes-only)
                    if (card.getAttribute('data-combo') === 'yes') {
                        shouldShow = false;
                    }
                }
            }
            
            // Show/hide card
            if (shouldShow) {
                card.style.display = 'block';
                visibleCount++;
                // Add fade in animation
                card.style.animation = 'fadeInUp 0.3s ease forwards';
            } else {
                card.style.display = 'none';
            }
        });
        
        updateFilterStatus(visibleCount);
        updateSelectedFiltersDisplay();
        updateFilterButtons();
    }
    
    // Filter button click handler with AND logic
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            if (filter === 'all') {
                // If clicking 'all', clear all other filters
                activeFilters.clear();
                activeFilters.add('all');
            } else {
                // Remove 'all' from active filters when selecting specific filters
                activeFilters.delete('all');
                
                // Toggle this filter
                if (activeFilters.has(filter)) {
                    activeFilters.delete(filter);
                    // If no filters left, show all
                    if (activeFilters.size === 0) {
                        activeFilters.add('all');
                    }
                } else {
                    activeFilters.add(filter);
                }
            }
            
            applyFilters();
        });
    });
    
    // Clear all filters button
    clearFiltersBtn.addEventListener('click', function() {
        activeFilters.clear();
        activeFilters.add('all');
        applyFilters();
    });
    
    // Initialize
    applyFilters();
    
    // Keep your existing Buy Now button functionality
    const buyButtons = document.querySelectorAll('.btn-buy-now-8-col');
    
    buyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const offerId = this.getAttribute('data-offer-id');
            const amount = this.getAttribute('data-amount');
            const ussd = this.getAttribute('data-ussd');
            const summary = this.getAttribute('data-summary');
            const validity = this.getAttribute('data-validity');
            const offerType = this.getAttribute('data-offer-type');
            const number = this.getAttribute('data-number');
            const operator = this.getAttribute('data-operator');

            // Update modal content
            document.getElementById('modal_offer_summary').textContent = summary;
            document.getElementById('modal_offer_amount').textContent = amount + ' Tk';
            document.getElementById('modal_offer_validity').textContent = validity;
            document.getElementById('modal_offer_type').textContent = offerType;
            document.getElementById('modal_offer_ussd').textContent = ussd ? '*' + ussd + '#' : 'N/A';

            // Set hidden form values
            document.getElementById('modal_offer_id').value = offerId;
            document.getElementById('modal_hidden_amount').value = amount;
            document.getElementById('modal_hidden_summary').value = summary;
            document.getElementById('modal_operator').value = operator;

            // Pre-fill number and update title
            document.getElementById('modal_targeted_number').value = number;
            document.getElementById('offerModalLabel').textContent = 'Purchase Offer';

            // Show modal
            $('#buyOfferModal').modal('show');
        });
    });

    // Phone number validation
    const phoneInputs = document.querySelectorAll('.phone-input-large, .phone-input-xl');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            let maxLength = value.startsWith('88') ? 13 : 11;
            
            if (value.length > maxLength) {
                value = value.slice(0, maxLength);
            }

            e.target.value = value;
        });
    });

    // Set operator badge color dynamically
    const operatorBadge = document.getElementById('operator-badge');
    if (operatorBadge) {
        const operatorText = operatorBadge.textContent.trim();
        operatorBadge.classList.remove('badge-primary', 'badge-success', 'badge-danger', 'badge-warning', 'badge-info');
        
        switch(operatorText) {
            case 'GP': operatorBadge.classList.add('badge-primary'); break;
            case 'Robi': operatorBadge.classList.add('badge-success'); break;
            case 'Airtel': operatorBadge.classList.add('badge-danger'); break;
            case 'BL': operatorBadge.classList.add('badge-warning'); break;
            case 'TT': operatorBadge.classList.add('badge-info'); break;
            default: operatorBadge.classList.add('badge-secondary');
        }
    }
});
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>