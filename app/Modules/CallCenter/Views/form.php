<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="d-flex align-items-center mb-4">
            <a href="<?= base_url('call-logs') ?>" class="btn btn-light rounded-circle p-2 me-3"><i data-lucide="arrow-left" width="20"></i></a>
            <h1 class="h3 mb-0 text-gray-800"><?= isset($call) ? 'Edit Call Log' : 'Log New Call' ?></h1>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <?php if(session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach(session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= isset($call) ? base_url('call-logs/update/' . $call->id) : base_url('call-logs/create') ?>" method="post">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Caller Name</label>
                            <input type="text" name="caller_name" class="form-control" value="<?= old('caller_name', $call->caller_name ?? '') ?>" placeholder="e.g. John Doe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="caller_number" class="form-control" value="<?= old('caller_number', $call->caller_number ?? '') ?>" placeholder="+1234567890" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Direction</label>
                            <select name="direction" class="form-select">
                                <option value="inbound" <?= (old('direction', $call->direction ?? '') == 'inbound') ? 'selected' : '' ?>>Inbound (Incoming)</option>
                                <option value="outbound" <?= (old('direction', $call->direction ?? '') == 'outbound') ? 'selected' : '' ?>>Outbound (Outgoing)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select">
                                <option value="answered" <?= (old('status', $call->status ?? '') == 'answered') ? 'selected' : '' ?>>Answered</option>
                                <option value="missed" <?= (old('status', $call->status ?? '') == 'missed') ? 'selected' : '' ?>>Missed</option>
                                <option value="voicemail" <?= (old('status', $call->status ?? '') == 'voicemail') ? 'selected' : '' ?>>Voicemail</option>
                                <option value="busy" <?= (old('status', $call->status ?? '') == 'busy') ? 'selected' : '' ?>>Busy</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Duration (Seconds)</label>
                        <input type="number" name="duration" class="form-control" value="<?= old('duration', $call->duration ?? 0) ?>" min="0">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Brief summary of the call..."><?= old('notes', $call->notes ?? '') ?></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2">
                            <?= isset($call) ? 'Update Call Log' : 'Save Call Log' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
