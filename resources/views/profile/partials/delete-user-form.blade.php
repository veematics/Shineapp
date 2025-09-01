<section>
    <div class="mb-4">
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>{{ __('Warning!') }}</strong>
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </div>
    </div>

    <button type="button" class="btn btn-danger" data-coreui-toggle="modal" data-coreui-target="#confirmUserDeletionModal">
        <i class="fas fa-trash-alt me-2"></i>
        {{ __('Delete Account') }}
    </button>

    <!-- Delete Account Confirmation Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true" @if($errors->userDeletion->isNotEmpty()) data-coreui-show="true" @endif>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmUserDeletionModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ __('Confirm Account Deletion') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-body">
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>{{ __('Are you sure you want to delete your account?') }}</strong>
                        </div>
                        
                        <p class="text-body-secondary mb-4">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <div class="mb-3">
                            <label for="delete_password" class="form-label visually-hidden">{{ __('Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-key text-danger"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                                       id="delete_password" 
                                       name="password" 
                                       placeholder="{{ __('Enter your password to confirm') }}" 
                                       required>
                            </div>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">
                            <i class="fas fa-times me-2"></i>
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-2"></i>
                            {{ __('Delete Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($errors->userDeletion->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new coreui.Modal(document.getElementById('confirmUserDeletionModal'));
                modal.show();
            });
        </script>
    @endif
</section>
