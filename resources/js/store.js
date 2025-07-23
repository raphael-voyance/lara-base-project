import Alpine from 'alpinejs';

Alpine.store('user', {
    name: 'Invité',
    setName(newName) {
        this.name = newName;
    }
});
